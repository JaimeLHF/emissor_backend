<?php

use App\Http\Controllers\AcabamentoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmitenteController;
use App\Http\Controllers\FaturaController;
use App\Http\Controllers\ItensVendaController;
use App\Http\Controllers\NFeController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\TransportadoraController;
use App\Http\Controllers\VendasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Acabamentos
Route::group(['prefix' => 'acabamentos'], function () {
    Route::get('/', [AcabamentoController::class, 'getAllAcabamentos']);
    Route::get('/{id}', [AcabamentoController::class, 'getBydId']);
    Route::post('/new', [AcabamentoController::class, 'newAcabamento']);
    Route::put('/update/{id}', [AcabamentoController::class, 'updateBydId']);
    Route::delete('/delete/{id}', [AcabamentoController::class, 'deleteBydId']);
});

//Produtos
Route::group(['prefix' => 'produtos'], function () {
    Route::get('/', [ProdutoController::class, 'getAllProducts']);
    Route::get('/{id}', [ProdutoController::class, 'getBydId']);
    Route::post('/new', [ProdutoController::class, 'newProduto']);
    Route::put('/update/{id}', [ProdutoController::class, 'updateBydId']);
    Route::delete('/delete/{id}', [ProdutoController::class, 'deleteBydId']);
});

//Clientes
Route::group(['prefix' => 'clientes'], function () {
    Route::get('/', [ClienteController::class, 'getAllClientes']);
    Route::get('/{id}', [ClienteController::class, 'getBydId']);
    Route::post('/new', [ClienteController::class, 'newCliente']);
    Route::put('/update/{id}', [ClienteController::class, 'updateById']);
    Route::delete('/delete/{id}', [ClienteController::class, 'deleteById']);
});

//Emitente
Route::group(['prefix' => 'emitente'], function () {
    Route::get('/', [EmitenteController::class, 'getEmitente']);
    Route::get('/{id}', [EmitenteController::class, 'getBydId']);
    Route::post('/new', [EmitenteController::class, 'newEmitente']);
    Route::put('/update/{id}', [EmitenteController::class, 'updateById']);
    Route::delete('/delete/{id}', [EmitenteController::class, 'deleteById']);
    Route::get('/{id}/download-certificado', [EmitenteController::class, 'downloadCertificado']);
    Route::put('/emitentes/{id}/ativar', [EmitenteController::class, 'atualizarEmitenteAtivo']);
});

//Item Venda
Route::group(['prefix' => 'item'], function () {
    Route::get('/', [ItensVendaController::class, 'getAllItens']);
    Route::get('/{id}', [ItensVendaController::class, 'getBydId']);
});

//Fatura Venda
Route::group(['prefix' => 'fatura'], function () {
    Route::get('/', [FaturaController::class, 'getAllFaturas']);
    Route::get('/{id}', [FaturaController::class, 'getBydId']);
});

//Venda
Route::group(['prefix' => 'venda'], function () {
    Route::get('/', [VendasController::class, 'getAllVendas']);
    Route::get('/{id}', [VendasController::class, 'getBydId']);
    Route::post('/new', [VendasController::class, 'save']);
    Route::put('/update/{id}', [VendasController::class, 'updateById']);
    Route::delete('/delete/{id}', [VendasController::class, 'deleteById']);
});

//Transportadoras
Route::group(['prefix' => 'trans'], function () {
    Route::get('/', [TransportadoraController::class, 'getAllTransportadoras']);
    Route::get('/{id}', [TransportadoraController::class, 'getById']);
    Route::post('/new', [TransportadoraController::class, 'newTrans']);
    Route::delete('/delete/{id}', [TransportadoraController::class, 'deleteById']);
    Route::put('/update/{id}', [TransportadoraController::class, 'updateById']);
});

//XML - Transmitir NFe
Route::group(['prefix' => 'nfe'], function () {
    Route::get('/gerarXml/{id}', [NFeController::class, 'gerarXml']);
    Route::get('/download/{id}', [NFeController::class, 'download']);
    Route::get('/imprimir/{id}', [NFeController::class, 'imprimir']);
    Route::get('/imprimirCancelamento/{id}', [NFeController::class, 'imprimirCancelamento']);
    Route::get('/consulta/{id}', [NFeController::class, 'consultaNFe']);
    Route::post('/transmitir', [NFeController::class, 'transmitir']);
    Route::post('/cancelar', [NFeController::class, 'cancelarNFe']);
    Route::get('/imprimirCCe/{id}', [NFeController::class, 'imprimirCCe']);
    Route::post('/cartaCorrecao', [NFeController::class, 'cartaCorrecao']);
});

