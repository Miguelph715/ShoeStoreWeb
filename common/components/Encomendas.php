<?php

declare(strict_types=1);

namespace common\components;

use common\models\Encomenda;
use common\models\ProdutoTamanho;

/**
 * Estados da encomenda: Pendente -> Confirmada -> Enviada -> Entregue (ou Cancelada).
 */
class Encomendas
{
    public const ESTADOS = ['Pendente', 'Confirmada', 'Enviada', 'Entregue', 'Cancelada'];

    /**
     * @return string[] estados permitidos a seguir a $estado
     */
    public static function seguintes(string $estado)
    {
        return match ($estado) {
            'Pendente' => ['Confirmada', 'Cancelada'],
            'Confirmada' => ['Enviada', 'Cancelada'],
            'Enviada' => ['Entregue'],
            default => [],
        };
    }

    /**
     * Muda o estado; devolve null ou o erro. Cancelar repõe o stock.
     */
    public static function mudarEstado(Encomenda $encomenda, string $novo)
    {
        if (!in_array($novo, self::seguintes($encomenda->estado), true)) {
            return 'Não se pode passar de "' . $encomenda->estado . '" para "' . $novo . '".';
        }

        $encomenda->estado = $novo;
        $encomenda->updated_at = time();
        if (!$encomenda->save(false, ['estado', 'updated_at'])) {
            return 'Não foi possível gravar o novo estado.';
        }

        //só repõe depois de gravar o estado, para não repor duas vezes
        if ($novo === 'Cancelada') {
            foreach ($encomenda->linhaEncomendas as $linha) {
                ProdutoTamanho::updateAllCounters(
                    ['stock' => $linha->quantidade],
                    ['produto_id' => $linha->produto_id, 'tamanho_id' => $linha->tamanho_id]
                );
            }
        }

        return null;
    }

    //classe Bootstrap do badge
    public static function classe(string $estado)
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
