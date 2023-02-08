package rotas

import (
	controllers "api/src/Controllers"
	"net/http"
)

var rotasUsuarios = []Rota{
	{
		URI:                "/usuarios", //Criar
		Metodo:             http.MethodPost,
		Funcao:             controllers.CriarUsuario,
		RequerAutenticacao: false,
	},
	{
		URI:                "/usuarios", //Pegar user
		Metodo:             http.MethodGet,
		Funcao:             controllers.BuscarUsuario,
		RequerAutenticacao: true,
	},
	{
		URI:                "/usuarios/{id}", //Pegar user por id
		Metodo:             http.MethodGet,
		Funcao:             controllers.BuscarUsuarioID,
		RequerAutenticacao: true,
	},
	{
		URI:                "/usuarios/{id}", //Modificar
		Metodo:             http.MethodPut,
		Funcao:             controllers.ModificarUsuario,
		RequerAutenticacao: true,
	},
	{
		URI:                "/usuarios/{id}", //Excluir
		Metodo:             http.MethodDelete,
		Funcao:             controllers.DeletarUsuario,
		RequerAutenticacao: true,
	},

	{
		URI:                "/usuarios/{id}/seguir", //Seguir usuarios
		Metodo:             http.MethodPost,
		Funcao:             controllers.SeguirUsuario,
		RequerAutenticacao: true,
	},

	{
		URI:                "/usuarios/{id}/deixar", // usuarios
		Metodo:             http.MethodPost,
		Funcao:             controllers.DeixarDeSeguir,
		RequerAutenticacao: true,
	},

	{
		URI:                "/usuarios/{id}/seguidores", // buscar seguidores
		Metodo:             http.MethodGet,
		Funcao:             controllers.BuscarSeguidores,
		RequerAutenticacao: true,
	},

	{
		URI:                "/usuarios/{id}/atualizarSenha", // Atualizar senha
		Metodo:             http.MethodPost,
		Funcao:             controllers.AtualizarSenha,
		RequerAutenticacao: true,
	},
}
