package controllers

import (
	"api/src/autenticacao"
	"api/src/banco"
	"api/src/model"
	"api/src/repositorios"
	"api/src/respostas"
	"api/src/seguranca"
	"encoding/json"
	"io/ioutil"
	"net/http"
)

func Login(w http.ResponseWriter, r *http.Request) {
	corpoReq, err := ioutil.ReadAll(r.Body)
	if err != nil {
		respostas.Erro(w, http.StatusUnprocessableEntity, err)
		return
	}

	var usuario model.Usuario
	if err = json.Unmarshal(corpoReq, &usuario); err != nil {
		respostas.Erro(w, http.StatusBadRequest, err)
		return
	}

	db, erro := banco.Conectar()

	if erro != nil {
		// StatusInternalServerError Erro interno
		respostas.Erro(w, http.StatusInternalServerError, erro)
		return
	}

	//cria o repositorio e passa o banco
	repositorio := repositorios.NovoRepositorioUsuario(db)

	usuarioSalvoBanco, err := repositorio.BuscarPorEmail(usuario.Email)
	if err != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
		return
	}

	if err = seguranca.VerificarSenha(usuarioSalvoBanco.Senha, usuario.Senha); err != nil {
		respostas.Erro(w, http.StatusUnauthorized, err)
		return
	}

	token, erro := autenticacao.CriarToken(usuarioSalvoBanco.ID)
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
		return
	}
	// fmt.Printf(token)
	w.Write([]byte(token))

}
