<?php

declare(strict_types=1);

//Dados de teste: utilizadores (um por role, mais um inativo e um eliminado), marcas, categorias,
//tamanhos, produtos com stock e metodos de pagamento.
//Corre-se na consola, DEPOIS das migrations e do rbac/init: php yii seed/demo

namespace console\controllers;

use common\models\User;
use Yii;
use yii\console\Controller;

class SeedController extends Controller
{
    //password de teste de todos os utilizadores abaixo (so para desenvolvimento)
    private const PASSWORD = '1234';

    // php yii seed/demo
    public function actionDemo(): int
    {
        $db = Yii::$app->db;
        $auth = Yii::$app->authManager;

        if ($auth->getRole('cliente') === null) {
            echo "Os roles ainda nao existem. Corre primeiro: php yii rbac/init\n";
            return self::EXIT_CODE_ERROR;
        }
        if (User::findByUsername('admin') !== null || (int) $db->createCommand('SELECT COUNT(*) FROM {{%marca}}')->queryScalar() > 0) {
            echo "Ja existem dados de teste. Para recomecar: php yii migrate/fresh, as migrations do RBAC, rbac/init e seed/demo.\n";
            return self::EXIT_CODE_ERROR;
        }

        $agora = time();
        $transacao = $db->beginTransaction();
        try {
            //utilizadores: username, email, status, role, nome, telefone, morada, codigo postal, cidade, nif
            $utilizadores = [
                ['admin', 'admin@shoestore.pt', User::STATUS_ACTIVE, 'admin', 'Administrador ShoeStore', '244000001', 'Rua da Loja 1', '2400-100', 'Leiria', null],
                ['gestor', 'gestor@shoestore.pt', User::STATUS_ACTIVE, 'gestor', 'Gestor de Loja', '244000002', 'Rua da Loja 1', '2400-100', 'Leiria', null],
                ['cliente1', 'cliente1@exemplo.pt', User::STATUS_ACTIVE, 'cliente', 'Ana Ferreira', '912345678', 'Rua das Flores 12', '2410-050', 'Leiria', '123456789'],
                ['cliente2', 'cliente2@exemplo.pt', User::STATUS_ACTIVE, 'cliente', 'Rui Santos', '923456789', 'Av. da Republica 45', '2430-203', 'Marinha Grande', null],
                //inativo: ainda nao verificou o email, nao consegue entrar
                ['inativo1', 'inativo1@exemplo.pt', User::STATUS_INACTIVE, 'cliente', 'Marta Lopes', '934567890', 'Rua do Mercado 7', '2495-300', 'Fatima', null],
                //eliminado: soft delete, nao consegue entrar
                ['eliminado1', 'eliminado1@exemplo.pt', User::STATUS_DELETED, 'cliente', 'Pedro Antunes', '965432109', 'Travessa Nova 3', '2400-300', 'Leiria', null],
            ];
            foreach ($utilizadores as [$username, $email, $status, $role, $nome, $telefone, $morada, $cp, $cidade, $nif]) {
                $user = new User();
                $user->username = $username;
                $user->email = $email;
                $user->status = $status;
                $user->setPassword(self::PASSWORD);
                $user->generateAuthKey();
                if ($status === User::STATUS_INACTIVE) {
                    $user->generateEmailVerificationToken();
                }
                //o TimestampBehavior do User preenche created_at e updated_at
                if (!$user->save()) {
                    throw new \RuntimeException("Utilizador $username: " . json_encode($user->errors));
                }
                $db->createCommand()->insert('{{%perfil}}', [
                    'user_id' => $user->id, 'nome' => $nome, 'telefone' => $telefone, 'morada' => $morada,
                    'codigo_postal' => $cp, 'cidade' => $cidade, 'nif' => $nif, 'created_at' => $agora, 'updated_at' => $agora,
                ])->execute();
                //ligar o utilizador ao role (tabela auth_assignment)
                $auth->assign($auth->getRole($role), $user->id);
            }

            //catalogo
            $marcas = ['Nike', 'Adidas', 'New Balance', 'Puma', 'Converse', 'Timberland', 'Clarks'];
            $db->createCommand()->batchInsert('{{%marca}}', ['nome'], array_map(fn($m) => [$m], $marcas))->execute();
            $categorias = [
                ['Ténis', 'Ténis e sapatilhas para o dia a dia e para desporto'],
                ['Botas', 'Botas e botins'],
                ['Formais', 'Sapatos clássicos e de vestir'],
                ['Chinelos', 'Chinelos e sandálias'],
            ];
            $db->createCommand()->batchInsert('{{%categoria}}', ['nome', 'descricao'], $categorias)->execute();
            $tamanhos = range(35, 47);
            $db->createCommand()->batchInsert('{{%tamanho}}', ['numero'], array_map(fn($n) => [$n], $tamanhos))->execute();
            $db->createCommand()->batchInsert('{{%metodo_pagamento}}', ['nome'], [['MB Way'], ['Multibanco'], ['Cartão de crédito']])->execute();

            //ids por nome
            $idMarca = array_column($db->createCommand('SELECT nome, id FROM {{%marca}}')->queryAll(), 'id', 'nome');
            $idCategoria = array_column($db->createCommand('SELECT nome, id FROM {{%categoria}}')->queryAll(), 'id', 'nome');
            //o MySQL devolve o numero como "38.0" (decimal), por isso converte-se para int
            $idTamanho = [];
            foreach ($db->createCommand('SELECT numero, id FROM {{%tamanho}}')->queryAll() as $t) {
                $idTamanho[(int) $t['numero']] = (int) $t['id'];
            }

            //marca, categoria, nome, descricao, preco, tamanho minimo, tamanho maximo
            $produtos = [
                ['Nike', 'Ténis', 'Nike Air Max 90', 'Ténis clássico com a icónica unidade de ar visível no calcanhar.', 139.99, 38, 46],
                ['Nike', 'Ténis', "Nike Air Force 1 '07", 'Ténis de cabedal com sola de amortecimento Air.', 119.99, 38, 47],
                ['Nike', 'Ténis', 'Nike Pegasus 40', 'Ténis de corrida versáteis, leves e com boa resposta.', 129.99, 38, 46],
                ['Nike', 'Chinelos', 'Nike Victori One Slide', 'Chinelo de espuma macia, ideal para depois do treino.', 29.99, 36, 47],
                ['Adidas', 'Ténis', 'Adidas Ultraboost Light', 'Ténis de corrida com amortecimento Boost.', 189.99, 38, 46],
                ['Adidas', 'Ténis', 'Adidas Superstar', 'Ténis de cabedal com a famosa biqueira de concha.', 99.99, 36, 46],
                ['Adidas', 'Ténis', 'Adidas Stan Smith', 'Ténis minimalistas de cabedal branco com detalhes verdes.', 99.99, 36, 46],
                ['Adidas', 'Chinelos', 'Adidas Adilette Shower', 'Chinelo leve e de secagem rápida.', 29.99, 36, 47],
                ['New Balance', 'Ténis', 'New Balance 574', 'Ténis retro, confortáveis e versáteis.', 109.99, 36, 46],
                ['New Balance', 'Ténis', 'New Balance 530', 'Ténis de inspiração running dos anos 2000.', 119.99, 36, 46],
                ['Puma', 'Ténis', 'Puma Suede Classic', 'Ténis de camurça com a forma clássica.', 79.99, 36, 46],
                ['Puma', 'Ténis', 'Puma RS-X', 'Ténis volumosos com estilo retro-futurista.', 119.99, 38, 46],
                ['Converse', 'Ténis', 'Converse Chuck Taylor All Star', 'O ténis de lona de cano alto que nunca passa de moda.', 74.99, 36, 46],
                ['Timberland', 'Botas', 'Timberland 6-Inch Premium', 'Bota de cabedal impermeável, cosida à mão.', 229.99, 39, 46],
                ['Clarks', 'Botas', 'Clarks Desert Boot', 'Bota de camurça com sola de crepe.', 134.99, 39, 46],
                ['Clarks', 'Formais', 'Clarks Oxford Clássico', 'Sapato Oxford de cabedal para ocasiões formais.', 119.99, 40, 45],
            ];
            //stock "variado" mas sempre igual (alguns tamanhos a 0 para testar "esgotado")
            $stocks = [0, 0, 2, 3, 4, 5, 6, 8, 10, 12];
            foreach ($produtos as $i => [$marca, $categoria, $nome, $descricao, $preco, $min, $max]) {
                $db->createCommand()->insert('{{%produto}}', [
                    'marca_id' => $idMarca[$marca], 'categoria_id' => $idCategoria[$categoria], 'nome' => $nome,
                    'descricao' => $descricao, 'preco' => $preco, 'ativo' => 1, 'created_at' => $agora, 'updated_at' => $agora,
                ])->execute();
                $idProduto = (int) $db->getLastInsertID();
                $linhas = [];
                for ($n = $min; $n <= $max; $n++) {
                    $linhas[] = [$idProduto, $idTamanho[$n], $stocks[(($i + 1) * 7 + $n * 3) % 10]];
                }
                $db->createCommand()->batchInsert('{{%produto_tamanho}}', ['produto_id', 'tamanho_id', 'stock'], $linhas)->execute();
            }

            $transacao->commit();
        } catch (\Throwable $e) {
            $transacao->rollBack();
            echo 'Erro: ' . $e->getMessage() . "\n";
            return self::EXIT_CODE_ERROR;
        }

        echo "Dados de teste criados: 6 utilizadores, 7 marcas, 4 categorias, 13 tamanhos, 16 produtos com stock e 3 metodos de pagamento.\n";
        return self::EXIT_CODE_NORMAL;
    }
}
