<?php

namespace App\Http\Controllers;

use App\Models\Fatura;
use App\Models\ItensVenda;
use App\Models\Produtos;
use App\Models\Vendas;
use Illuminate\Http\Request;

class VendasController extends Controller
{
    public function save(Request $request)
    {
        try {
            $venda = $request->venda;

            $valorTotal = 0;

            // Calcula a soma dos valores dos itens da venda
            foreach ($venda['itens'] as $i) {
                $valorTotal += floatval(str_replace(",", ".", $i['valor'])) * intval($i['qtd']);
            }

            // Cria a venda com o valorTotal calculado
            $result = Vendas::create([
                'valorTotal' => $valorTotal,
                'cliente_id' => $venda['cliente_id'],
                'sequencia_evento' => $venda['sequencia_evento'],
                'natOp' => $venda['natOp'],
                'finNFe' => $venda['finNFe'],
                'chave' => '',
                'numero_nfe' => 0,
                'modFrete' => $venda['modFrete'],
                'vFrete' => $venda['vFrete'],
                'status' => 'Novo',
                'infCpl' => $venda['infCpl'],
                'transp_id' => $venda['transp_id']
            ]);

            //Itens da venda
            foreach ($venda['itens'] as $i) {

                ItensVenda::create([
                    'valor' => str_replace(",", ".", $i['valor']),
                    'valorTotal' => str_replace(",", ".", $i['valor']) * $i['qtd'],
                    'qtd' => $i['qtd'],
                    'venda_id' => $result->id,
                    'produto_id' => $i['id']
                ]);
            }

            //Fatura/Duplicatas da venda
            foreach ($venda['fatura'] as $f) {
                $produto = Produtos::findOrFail($i['id']);
                $valorIPI = floatval(str_replace(",", ".", $f['valor'])) * ($produto->perc_ipi / 100);

                Fatura::create([
                    'valor' => str_replace(",", ".", $f['valor']),
                    'venda_id' => $result->id,
                    'vencimento' => \Carbon\Carbon::parse(str_replace("/", "-", $f['vencimento']))->format('Y-m-d'),
                    'forma_pagamento' => $f['forma_pagamento'],
                    'status' => "Aberto",
                    'valor_ipi' => $valorIPI + str_replace(",", ".", $f['valor'])
                ]);
            }

            return response()->json(['message' => 'Venda criada com sucesso', 'venda_ID' => $result->id], 201);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 404);
        }
    }

    public function getAllVendas()
    {
        try {

            $venda = Vendas::with('cliente', 'itens.produto', 'itens.produto.acabamento', 'fatura', 'transportadora')->get();

            if (!$venda) {
                return response()->json(['message' => 'venda não encontrada'], 404);
            }

            return response()->json($venda, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno no servidor', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateById(Request $request, $id)
    {
        try {
            $vendaData = $request->venda;

            $venda = Vendas::find($id);

            if (!$venda) {
                return response()->json(['message' => 'Venda não encontrada!'], 404);
            }

            // Calcula o valor total com base nos itens
            $valorTotal = 0;
            foreach ($vendaData['itens'] as $i) {
                $valorTotal += floatval(str_replace(",", ".", $i['valor'])) * intval($i['qtd']);
            }

            // Atualiza a venda com o valorTotal calculado
            $venda->update([
                'valorTotal' => $valorTotal,
                'cliente_id' => $vendaData['cliente_id'],
                'sequencia_evento' => $vendaData['sequencia_evento'],
                'natOp' => $vendaData['natOp'],
                'finNFe' => $vendaData['finNFe'],
                'chave' => $vendaData['chave'],
                'numero_nfe' => $vendaData['numero_nfe'],
                'modFrete' => $vendaData['modFrete'],
                'vFrete' => $vendaData['vFrete'],
                'status' => $vendaData['status'],
                'infCpl' => $vendaData['infCpl'],
                'transp_id' => $vendaData['transp_id']
            ]);

            // Atualiza Itens da venda
            $venda->itens()->delete(); // Remove os itens antigos
            foreach ($vendaData['itens'] as $i) {
                ItensVenda::create([
                    'valor' => str_replace(",", ".", $i['valor']),
                    'valorTotal' => str_replace(",", ".", $i['valor']) * $i['qtd'],
                    'qtd' => $i['qtd'],
                    'venda_id' => $venda->id,
                    'produto_id' => $i['id']
                ]);
            }

            // Atualiza Faturas/Duplicatas da venda
            $venda->fatura()->delete(); // Remove as faturas antigas
            foreach ($vendaData['fatura'] as $f) {
                $produto = Produtos::find($i['id']); // Assumindo que fatura precisa de produto_id
                $valorIPI = $produto ? floatval(str_replace(",", ".", $f['valor'])) * ($produto->perc_ipi / 100) : 0;

                Fatura::create([
                    'valor' => str_replace(",", ".", $f['valor']),
                    'venda_id' => $venda->id,
                    'vencimento' => \Carbon\Carbon::parse(str_replace("/", "-", $f['vencimento']))->format('Y-m-d'),
                    'forma_pagamento' => $f['forma_pagamento'],
                    'status' => "Aberto",
                    'valor_ipi' => $valorIPI + str_replace(",", ".", $f['valor'])
                ]);
            }

            return response()->json(['message' => 'Venda atualizada com sucesso', 'venda_ID' => $venda->id], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar venda', 'error' => $e->getMessage()], 500);
        }
    }

    public function getBydId($id)
    {
        try {

            $venda = Vendas::with('cliente', 'itens.produto', 'itens.produto.acabamento', 'fatura', 'transportadora')->find($id);

            if (!$venda) {
                return response()->json(['message' => 'Venda não econtrada!'], 404);
            }

            return response()->json($venda, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno no servidor', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteById($id)
    {
        try {
            $venda = Vendas::find($id);

            if (!$venda) {
                return response()->json(['message' => 'Venda não econtrado!'], 404);
            }

            $venda->delete();

            return response()->json(['message' => 'Venda deletado com sucesso!', 'venda' => $venda], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno no servidor', 'error' => $e->getMessage()], 500);
        }
    }
}
