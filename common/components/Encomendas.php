<?php

declare(strict_types=1);

namespace common\components;

use common\models\Encomenda;
use Yii;

/**
 * As regras do estado de uma encomenda (usadas pelo back-office e pelo front-office).
 *
 * Fluxo: Pendente -> Confirmada -> Enviada -> Entregue. Pode-se cancelar enquanto esta Pendente
 * ou Confirmada; ao cancelar, o stock descontado volta para a loja.
 */
class Encomendas
{
    public const ESTADOS = ['Pendente', 'Confirmada', 'Enviada', 'Entregue', 'Cancelada'];

    /**
     * Os estados para onde uma encomenda pode passar a partir do estado atual.
     *
     * @return string[]
     */
    public static function seguintes(string $estado): array
    {
        return match ($estado) {
            'Pendente' => ['Confirmada', 'Cancelada'],
            'Confirmada' => ['Enviada', 'Cancelada'],
            'Enviada' => ['Entregue'],
            default => [],
        };
    }

    /**
     * Muda o estado da encomenda. Devolve null se correu bem, ou o texto do erro.
     * Ao cancelar, devolve o stock de cada linha (tudo numa transacao).
     */
    public static function mudarEstado(Encomenda $encomenda, string $novo): string|null
    {
        if (!in_array($novo, self::seguintes($encomenda->estado), true)) {
            return 'Não se pode passar de "' . $encomenda->estado . '" para "' . $novo . '".';
        }

        $db = Yii::$app->db;
        $transacao = $db->beginTransaction();
        try {
            if ($novo === 'Cancelada') {
                foreach ($encomenda->linhaEncomendas as $linha) {
                    $db->createCommand(
                        'UPDATE {{%produto_tamanho}} SET stock = stock + :q WHERE produto_id = :p AND tamanho_id = :t',
                        [':q' => $linha->quantidade, ':p' => $linha->produto_id, ':t' => $linha->tamanho_id]
                    )->execute();
                }
            }
            $encomenda->estado = $novo;
            $encomenda->updated_at = time();
            if (!$encomenda->save(false, ['estado', 'updated_at'])) {
                throw new \RuntimeException('Não foi possível gravar o novo estado.');
            }
            $transacao->commit();

            return null;
        } catch (\Throwable $e) {
            $transacao->rollBack();

            return $e->getMessage();
        }
    }

    /**
     * A cor do "badge" de cada estado (classes do Bootstrap).
     */
    public static function classe(string $estado): string
    {
        return match ($estado) {
            'Pendente' => 'warning',
            'Confirmada' => 'info',
            'Enviada' => 'primary',
            'Entregue' => 'success',
            default => 'secondary',
        };
    }
}
