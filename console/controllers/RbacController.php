<?php

declare(strict_types=1);

//Cria os roles e as permissoes do RBAC (DbManager) e liga uns aos outros.
//Corre-se na consola: php yii rbac/init

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\db\Query;

class RbacController extends Controller
{
    // php yii rbac/init
    public function actionInit()
    {
        //ponto de acesso ao RBAC: o componente authManager (common/config/main.php)
        $auth = Yii::$app->authManager;
        //guardar os roles ja atribuidos, porque o removeAll tambem os apaga
        $atribuicoes = (new Query())->select(['item_name', 'user_id'])->from($auth->assignmentTable)->all($auth->db);
        //apagar o que ja existir, para poder correr o comando mais do que uma vez
        $auth->removeAll();

        //permissoes do cliente (front-office e app Android)
        $gerirPerfil = $this->permissao($auth, 'gerirPerfil', 'Ver e editar o proprio perfil');
        $gerirFavoritos = $this->permissao($auth, 'gerirFavoritos', 'Marcar, desmarcar e listar favoritos');
        $gerirCarrinho = $this->permissao($auth, 'gerirCarrinho', 'Gerir o carrinho de compras');
        $criarEncomenda = $this->permissao($auth, 'criarEncomenda', 'Finalizar uma encomenda');
        $verEncomendasProprias = $this->permissao($auth, 'verEncomendasProprias', 'Ver o historico e o detalhe das proprias encomendas');
        $cancelarEncomendaPropria = $this->permissao($auth, 'cancelarEncomendaPropria', 'Cancelar uma encomenda propria que esteja Pendente');

        //permissoes do gestor (back-office)
        $acederBackoffice = $this->permissao($auth, 'acederBackoffice', 'Entrar no back-office');
        $verDashboard = $this->permissao($auth, 'verDashboard', 'Ver o dashboard do back-office');
        $marcas = $this->crud($auth, 'marcas', ['verMarcas', 'criarMarca', 'editarMarca', 'eliminarMarca']);
        $categorias = $this->crud($auth, 'categorias', ['verCategorias', 'criarCategoria', 'editarCategoria', 'eliminarCategoria']);
        $tamanhos = $this->crud($auth, 'tamanhos', ['verTamanhos', 'criarTamanho', 'editarTamanho', 'eliminarTamanho']);
        $produtos = $this->crud($auth, 'produtos', ['verProdutos', 'criarProduto', 'editarProduto', 'eliminarProduto']);
        $gerirStock = $this->permissao($auth, 'gerirStock', 'Gerir o stock por tamanho');
        $gerirEncomendas = $this->permissao($auth, 'gerirEncomendas', 'Listar, ver, alterar o estado e cancelar encomendas');

        //permissoes so do admin
        $utilizadores = $this->crud($auth, 'utilizadores', ['verUtilizadores', 'criarUtilizador', 'editarUtilizador', 'eliminarUtilizador']);
        $atribuirRoles = $this->permissao($auth, 'atribuirRoles', 'Atribuir e alterar roles dos utilizadores');
        $metodosPagamento = $this->crud($auth, 'metodos de pagamento', ['verMetodosPagamento', 'criarMetodoPagamento', 'editarMetodoPagamento', 'eliminarMetodoPagamento']);

        //roles e as permissoes de cada um
        $cliente = $auth->createRole('cliente');
        $cliente->description = 'Cliente: usa o front-office e a aplicacao Android';
        $auth->add($cliente);
        foreach ([$gerirPerfil, $gerirFavoritos, $gerirCarrinho, $criarEncomenda, $verEncomendasProprias, $cancelarEncomendaPropria] as $p) {
            $auth->addChild($cliente, $p);
        }

        $gestor = $auth->createRole('gestor');
        $gestor->description = 'Gestor de loja: catalogo, stock e encomendas no back-office';
        $auth->add($gestor);
        foreach ([$acederBackoffice, $verDashboard, ...$marcas, ...$categorias, ...$tamanhos, ...$produtos, $gerirStock, $gerirEncomendas] as $p) {
            $auth->addChild($gestor, $p);
        }

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrador: utilizadores, roles e metodos de pagamento (e tudo o que o gestor faz)';
        $auth->add($admin);
        //o admin herda o gestor e acrescenta as suas permissoes
        $auth->addChild($admin, $gestor);
        foreach ([...$utilizadores, $atribuirRoles, ...$metodosPagamento] as $p) {
            $auth->addChild($admin, $p);
        }

        //repor os roles que os utilizadores ja tinham
        foreach ($atribuicoes as $atribuicao) {
            $role = $auth->getRole($atribuicao['item_name']);
            if ($role !== null) {
                $auth->assign($role, $atribuicao['user_id']);
            }
        }

        echo "RBAC criado: 3 roles e 35 permissoes. Atribuicoes repostas: " . count($atribuicoes) . ".\n";
    }

    // php yii rbac/atribuir admin 1
    public function actionAtribuir(string $nomeRole, int $idUtilizador)
    {
        $auth = Yii::$app->authManager;
        //buscar o role que ja foi criado no actionInit
        $role = $auth->getRole($nomeRole);
        if ($role === null) {
            echo "O role $nomeRole nao existe. Corre primeiro: php yii rbac/init\n";
            return;
        }
        //ligar o role ao utilizador (grava na tabela auth_assignment)
        $auth->assign($role, $idUtilizador);
        echo "Role $nomeRole atribuido ao utilizador $idUtilizador.\n";
    }

    //cria as 4 permissoes de um CRUD, pela ordem ver, criar, editar, eliminar
    private function crud($auth, string $recurso, array $nomes)
    {
        $permissoes = [];
        foreach (['Ver', 'Criar', 'Editar', 'Eliminar'] as $i => $verbo) {
            $permissoes[] = $this->permissao($auth, $nomes[$i], "$verbo $recurso");
        }
        return $permissoes;
    }

    //cria a permissao e grava-a na tabela auth_item
    private function permissao($auth, string $nome, string $descricao)
    {
        $p = $auth->createPermission($nome);
        $p->description = $descricao;
        $auth->add($p);
        return $p;
    }
}
