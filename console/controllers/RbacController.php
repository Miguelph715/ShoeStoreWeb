<?php

declare(strict_types=1);

//Cria os roles e as permissoes do RBAC (DbManager) e liga uns aos outros.
//Corre-se na consola: php yii rbac/init

namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    // php yii rbac/init
    public function actionInit()
    {
        //ponto de acesso ao RBAC: o componente authManager (common/config/main.php)
        $auth = Yii::$app->authManager;
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
        $gerirMarcas = $this->permissao($auth, 'gerirMarcas', 'CRUD de marcas');
        $gerirCategorias = $this->permissao($auth, 'gerirCategorias', 'CRUD de categorias');
        $gerirTamanhos = $this->permissao($auth, 'gerirTamanhos', 'CRUD de tamanhos');
        $gerirProdutos = $this->permissao($auth, 'gerirProdutos', 'CRUD de produtos');
        $gerirStock = $this->permissao($auth, 'gerirStock', 'Gerir o stock por tamanho');
        $gerirEncomendas = $this->permissao($auth, 'gerirEncomendas', 'Listar, ver, alterar o estado e cancelar encomendas');

        //permissoes so do admin
        $gerirUtilizadores = $this->permissao($auth, 'gerirUtilizadores', 'CRUD de utilizadores com soft delete');
        $atribuirRoles = $this->permissao($auth, 'atribuirRoles', 'Atribuir e alterar roles dos utilizadores');
        $gerirMetodosPagamento = $this->permissao($auth, 'gerirMetodosPagamento', 'CRUD de metodos de pagamento');

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
        foreach ([$acederBackoffice, $verDashboard, $gerirMarcas, $gerirCategorias, $gerirTamanhos, $gerirProdutos, $gerirStock, $gerirEncomendas] as $p) {
            $auth->addChild($gestor, $p);
        }

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrador: utilizadores, roles e metodos de pagamento (e tudo o que o gestor faz)';
        $auth->add($admin);
        //o admin herda o gestor e acrescenta as suas tres permissoes
        $auth->addChild($admin, $gestor);
        foreach ([$gerirUtilizadores, $atribuirRoles, $gerirMetodosPagamento] as $p) {
            $auth->addChild($admin, $p);
        }

        echo "RBAC criado: 3 roles e 17 permissoes.\n";
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

    //cria a permissao e grava-a na tabela auth_item
    private function permissao($auth, string $nome, string $descricao)
    {
        $p = $auth->createPermission($nome);
        $p->description = $descricao;
        $auth->add($p);
        return $p;
    }
}
