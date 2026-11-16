<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\Perfil;
use yii\base\Model;

/**
 * Editar o perfil do cliente: nome, telefone, morada, codigo postal, cidade e NIF.
 */
class PerfilForm extends Model
{
    public string $nome = '';
    public $telefone;
    public $morada;
    public $codigo_postal;
    public $cidade;
    public $nif;

    /**
     * Cria o formulario ja preenchido com os dados do perfil.
     */
    public static function fromPerfil(Perfil $perfil): self
    {
        $form = new self();
        foreach (['nome', 'telefone', 'morada', 'codigo_postal', 'cidade', 'nif'] as $campo) {
            $form->$campo = $perfil->$campo;
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['nome', 'telefone', 'morada', 'codigo_postal', 'cidade', 'nif'], 'trim'],
            ['nome', 'required'],
            ['nome', 'string', 'max' => 128],
            ['telefone', 'match', 'pattern' => '/^\+?[0-9 ]{9,20}$/', 'message' => 'Indica um telefone válido (só números).'],
            ['morada', 'string', 'max' => 255],
            ['codigo_postal', 'match', 'pattern' => '/^\d{4}-\d{3}$/', 'message' => 'Usa o formato 0000-000.'],
            ['cidade', 'string', 'max' => 64],
            ['nif', 'match', 'pattern' => '/^\d{9}$/', 'message' => 'O NIF tem 9 números.'],
            [['telefone', 'morada', 'codigo_postal', 'cidade', 'nif'], 'default', 'value' => null],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'nome' => 'Nome',
            'telefone' => 'Telefone',
            'morada' => 'Morada',
            'codigo_postal' => 'Código postal',
            'cidade' => 'Cidade',
            'nif' => 'NIF',
        ];
    }

    /**
     * Grava no perfil. Devolve true se correu bem.
     */
    public function guardar(Perfil $perfil): bool
    {
        if (!$this->validate()) {
            return false;
        }
        foreach (['nome', 'telefone', 'morada', 'codigo_postal', 'cidade', 'nif'] as $campo) {
            $perfil->$campo = $this->$campo;
        }

        return $perfil->save();
    }
}
