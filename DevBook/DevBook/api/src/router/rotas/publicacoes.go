package rotas

import (
	controllers "api/src/Controllers"
	"net/http"
)

var rotasPublicacoes = []Rota{
	{
		URI:                "/publicacoes", //criar
		Metodo:             http.MethodPost,
		Funcao:             controllers.CriarPublicacoes,
		RequerAutenticacao: true,
	},
	{
		URI:                "/publicacoes", //feed
		Metodo:             http.MethodGet,
		Funcao:             controllers.BuscarPublicacoes,
		RequerAutenticacao: true,
	},
	{
		URI:                "/publicacoes/{id}", //buscar
		Metodo:             http.MethodGet,
		Funcao:             controllers.BuscarPublicacao,
		RequerAutenticacao: true,
	},
	{
		URI:                "/publicacoes", //atualizar
		Metodo:             http.MethodPut,
		Funcao:             controllers.AtualizarPublicacao,
		RequerAutenticacao: true,
	},
	{
		URI:                "/publicacoes", //deletar
		Metodo:             http.MethodGet,
		Funcao:             controllers.DeletarPublicacao,
		RequerAutenticacao: true,
	},
}
