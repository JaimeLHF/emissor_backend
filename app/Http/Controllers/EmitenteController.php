<?php

namespace App\Http\Controllers;

use App\Models\Emitente;
use Illuminate\Http\Request;

class EmitenteController extends Controller
{
    public function getEmitente()
    {
        try {
            $emitente = Emitente::get();

            if (!$emitente) {
                return response()->json(['message' => 'Emitente não encontrado'], 404);
            }

            $emitente->makeHidden(['certificado']);

            return response()->json($emitente, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno no servidor', 'error' => $e->getMessage()], 500);
        }
    }

    public function getBydId($id)
    {
        try {
            $emitente = Emitente::find($id);

            if (!$emitente) {
                return response()->json(['message' => 'Emitente não econtrado!'], 404);
            }

            // $emitente->makeHidden(['certificado']);

            return response()->json($emitente, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno no servidor', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Atualiza o emitente ativo e define os outros como inativos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function atualizarEmitenteAtivo($id)
    {
        // Define todos os emitentes como inativos
        Emitente::where('ativo', true)->update(['ativo' => false]);

        // Define o emitente específico como ativo
        $emitente = Emitente::findOrFail($id);
        $emitente->ativo = true;
        $emitente->save();

        return response()->json([
            'message' => 'Emitente atualizado com sucesso.',
            'emitente' => $emitente
        ]);
    }


    public function updateById(Request $request, $id)
    {
        try {
            // Encontra o emitente pelo ID
            $emitente = Emitente::find($id);
    
            if (!$emitente) {
                return response()->json(['message' => 'Emitente não encontrado!'], 404);
            }
    
            // Atualiza os dados do emitente com exceção do certificado
            $emitente->update($request->except('certificado'));
    
            // Verifica se um novo certificado foi enviado
            if ($request->hasFile('certificado')) {
                // Deleta o certificado antigo se existir
                if ($emitente->certificado && file_exists(public_path($emitente->certificado))) {
                    unlink(public_path($emitente->certificado));
                }
    
                // Salva o novo certificado
                $file = $request->file('certificado');
                $filename = $emitente->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('certificados'), $filename);
    
                // Atualiza o caminho do certificado no emitente
                $emitente->certificado = 'certificados/' . $filename;
                $emitente->save();
            }
    
            return response()->json($emitente, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno no servidor', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function newEmitente(Request $request)
    {
        try {
            // Valida os dados do request
            $request->validate(Emitente::rules());

            // Define o valor padrão de sequencia_evento
            $data = $request->all();
            $data['sequencia_evento'] = $data['sequencia_evento'] ?? 1;

            // Cria o emitente sem o certificado
            $emitente = Emitente::create($data);

            // Verifica se o certificado foi enviado
            if ($request->hasFile('certificado')) {
                $file = $request->file('certificado');
                $filename = $emitente->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('certificados'), $filename);

                // Atualiza o caminho do certificado no emitente
                $emitente->certificado = 'certificados/' . $filename;
                $emitente->save();
            }

            return response()->json($emitente, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno no servidor', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteById($id)
    {
        try {
            $emitente = Emitente::find($id);

            if (!$emitente) {
                return response()->json(['message' => 'Emitente não encontrado!'], 404);
            }

            // Caminho do certificado
            $certificadoPath = public_path($emitente->certificado);

            // Deleta o emitente
            $emitente->delete();

            // Verifica se o arquivo de certificado existe e deleta
            if (file_exists($certificadoPath)) {
                unlink($certificadoPath);
            }

            return response()->json(['message' => 'Emitente deletado com sucesso!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno no servidor', 'error' => $e->getMessage()], 500);
        }
    }

    public function downloadCertificado($id)
    {
        try {
            // Encontra o emitente pelo ID
            $emitente = Emitente::findOrFail($id);

            // Verifica se o emitente tem um certificado
            if (!$emitente->certificado) {
                return response()->json(['message' => 'Certificado não encontrado para este emitente.'], 404);
            }

            // Caminho do certificado
            $path = public_path($emitente->certificado);

            // Verifica se o arquivo existe
            if (!file_exists($path)) {
                return response()->json(['message' => 'Arquivo de certificado não encontrado.'], 404);
            }

            // Retorna o arquivo para download
            return response()->download($path);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno no servidor', 'error' => $e->getMessage()], 500);
        }
    }
}
