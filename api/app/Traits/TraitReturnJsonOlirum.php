<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait: TraitReturnJsonOlirum
 * 
 * Descrição: Padroniza a estrutura de respostas JSON em toda a API. 
 * Garante que o frontend receba sempre o mesmo formato de objeto, facilite o tratamento 
 * de erros e assegure a integridade dos códigos HTTP e caracteres Unicode.
 * 
 * @author Murilo Dark
 * @date 2024-06-27 (Criado)
 */
trait TraitReturnJsonOlirum
{
    /**
     * Gera uma resposta JSON padronizada para a plataforma.
     *
     * @param mixed $data Conteúdo da resposta (arrays, objetos ou null).
     * @param string $message Mensagem descritiva (sucesso ou erro).
     * @param bool $status Indicador booleano de sucesso da operação.
     * @param int $code Código de status HTTP (ex: 200, 403, 422).
     * @return JsonResponse
     */
    public function ReturnJson($data = null, $message = '', $status = true, $code = 200): JsonResponse
    {
        /**
         * VALIDAÇÃO DE SEGURANÇA:
         * Garante que o código HTTP esteja dentro da faixa oficial permitida (100-599).
         * Caso contrário, força o status 200 para evitar quebras no protocolo de resposta.
         */
        if ($code < 100 || $code > 599) {
            $code = 200;
        }

        /**
         * NORMALIZAÇÃO:
         * Garante que o status seja sempre um valor estritamente booleano (true/false).
         */
        $status = (bool) $status;

        /**
         * CONSTRUÇÃO DA RESPOSTA:
         * - data: Payload de dados.
         * - message: Texto informativo.
         * - status: Controle de fluxo para o frontend.
         * - code: Código de status para facilitar o debug.
         * 
         * Opção JSON_UNESCAPED_UNICODE: Mantém acentos e caracteres especiais 
         * legíveis no JSON sem escapá-los para formato hexadecimal.
         */
        return response()->json([
            'data'    => $data,
            'message' => $message,
            'status'  => $status,
            'code'    => $code
        ], $code, [], JSON_UNESCAPED_UNICODE);
    }
}
