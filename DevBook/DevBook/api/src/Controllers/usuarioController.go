package controllers

import (
	"api/src/autenticacao"
	"api/src/banco"
	"api/src/model"
	"api/src/repositorios"
	"api/src/respostas"
	"api/src/seguranca"
	"encoding/json"
	"errors"
	"fmt"
	"io/ioutil"
	"net/http"
	"strconv"
	"strings"

	"github.com/gorilla/mux"
)

func CriarUsuario(w http.ResponseWriter, r *http.Request) {
	corpoRequest, erro := ioutil.ReadAll(r.Body)
	if erro != nil {

		respostas.Erro(w, http.StatusUnprocessableEntity, erro)
		return
	}

	var usuario model.Usuario

	if erro = json.Unmarshal(corpoRequest, &usuario); erro != nil {
		// StatusBadRequest
		respostas.Erro(w, http.StatusBadRequest, erro)
		return
	}

	if erro := usuario.Preparar("cadastro"); erro != nil {
		respostas.Erro(w, http.StatusBadRequest, erro)
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

	// chama o metodo criar passando o usuario lido no Request
	usuario.ID, erro = repositorio.Criar(usuario)
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
		return
	}

	respostas.JSON(w, http.StatusCreated, usuario)
}
func BuscarUsuario(w http.ResponseWriter, r *http.Request) {
	nomeOuNick := strings.ToLower(r.URL.Query().Get("usuario"))

	db, erro := banco.Conectar()
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
	}
	defer db.Close()
	repositorio := repositorios.NovoRepositorioUsuario(db)

	usuarios, err := repositorio.BuscarUser(nomeOuNick)
	if err != nil {
		respostas.Erro(w, http.StatusInternalServerError, err)
	}

	respostas.JSON(w, http.StatusOK, usuarios)
}
func BuscarUsuarioID(w http.ResponseWriter, r *http.Request) {
	parametro := mux.Vars(r)
	ID, err := strconv.ParseUint(parametro["id"], 10, 64)
	if err != nil {
		respostas.Erro(w, http.StatusBadRequest, err)
		return
	}

	db, erro := banco.Conectar()

	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
	}
	defer db.Close()

	repositorio := repositorios.NovoRepositorioUsuario(db)

	usuario, err := repositorio.BuscarUserID(ID)
	if err != nil {
		respostas.Erro(w, http.StatusInternalServerError, err)
	}

	respostas.JSON(w, http.StatusOK, usuario)
}
func ModificarUsuario(w http.ResponseWriter, r *http.Request) {
	//pega o parametro da url
	parametro := mux.Vars(r)

	ID, err := strconv.ParseUint(parametro["id"], 10, 64)
	if err != nil {
		respostas.Erro(w, http.StatusBadRequest, err)
		return
	}
	// Pega usuarioID do Token
	usuarioIdToken, err := autenticacao.ExtrairUsuarioID(r)
	if err != nil {
		respostas.Erro(w, http.StatusUnauthorized, err)
		return
	}
	fmt.Println("ID do token ", usuarioIdToken)
	if usuarioIdToken != ID {
		respostas.Erro(w, http.StatusForbidden, errors.New("ID do Token não confere com id de modificação do usuario"))
		return
	}

	// pega os dados da request
	corpoRequest, erro := ioutil.ReadAll(r.Body)
	if erro != nil {
		respostas.Erro(w, http.StatusUnprocessableEntity, erro)

	}

	var usuario model.Usuario

	//passa request para struct usuario
	if erro = json.Unmarshal(corpoRequest, &usuario); erro != nil {
		respostas.Erro(w, http.StatusBadRequest, erro)
		return
	}

	//verifico os campos preenchidos e formato
	if erro := usuario.Preparar("atualizar"); erro != nil {
		respostas.Erro(w, http.StatusBadRequest, erro)
		return
	}

	//abro o banco
	db, erro := banco.Conectar()
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
	}
	defer db.Close()

	repositorio := repositorios.NovoRepositorioUsuario(db)
	if err = repositorio.ModificarUser(ID, usuario); err != nil {
		respostas.Erro(w, http.StatusInternalServerError, err)
	}

	respostas.JSON(w, http.StatusOK, nil)
}
func DeletarUsuario(w http.ResponseWriter, r *http.Request) {
	//pega o parametro da url
	parametro := mux.Vars(r)

	ID, err := strconv.ParseUint(parametro["id"], 10, 64)
	if err != nil {
		respostas.Erro(w, http.StatusBadRequest, err)
		return
	}

	// Pega usuarioID do Token
	usuarioIdToken, err := autenticacao.ExtrairUsuarioID(r)
	if err != nil {
		respostas.Erro(w, http.StatusUnauthorized, err)
		return
	}
	fmt.Println("ID do token ", usuarioIdToken)
	if usuarioIdToken != ID {
		respostas.Erro(w, http.StatusForbidden, errors.New("ID do Token não confere com ID de exclusão do usuario"))
		return
	}

	//abro o banco
	db, erro := banco.Conectar()
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
	}
	defer db.Close()

	repositorio := repositorios.NovoRepositorioUsuario(db)
	if err = repositorio.DeletarUser(ID); err != nil {
		respostas.Erro(w, http.StatusInternalServerError, err)
	}

	respostas.JSON(w, http.StatusOK, nil)
}

func SeguirUsuario(w http.ResponseWriter, r *http.Request) {
	// Pega usuarioID do Token
	usuarioIdToken, err := autenticacao.ExtrairUsuarioID(r)
	if err != nil {
		respostas.Erro(w, http.StatusUnauthorized, err)
		return
	}
	fmt.Println("ID do token ", usuarioIdToken)

	parametro := mux.Vars(r)
	usuarioID, erro := strconv.ParseUint(parametro["id"], 10, 64)
	if erro != nil {
		respostas.Erro(w, http.StatusBadRequest, erro)
	}

	if usuarioIdToken == usuarioID {
		respostas.Erro(w, http.StatusForbidden, errors.New("Não é possível seguir você mesmo"))
		return
	}

	//abro o banco
	db, erro := banco.Conectar()
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
	}
	defer db.Close()

	repositorio := repositorios.NovoRepositorioUsuario(db)
	if err = repositorio.Seguir(usuarioID, usuarioIdToken); err != nil {
		respostas.Erro(w, http.StatusInternalServerError, err)
	}

	respostas.JSON(w, http.StatusNoContent, nil)
}

func DeixarDeSeguir(w http.ResponseWriter, r *http.Request) {
	// Pega usuarioID do Token
	usuarioIdToken, err := autenticacao.ExtrairUsuarioID(r)
	if err != nil {
		respostas.Erro(w, http.StatusUnauthorized, err)
		return
	}
	fmt.Println("ID do token ", usuarioIdToken)

	parametro := mux.Vars(r)
	usuarioID, erro := strconv.ParseUint(parametro["id"], 10, 64)
	if erro != nil {
		respostas.Erro(w, http.StatusBadRequest, erro)
	}

	if usuarioIdToken == usuarioID {
		respostas.Erro(w, http.StatusForbidden, errors.New("ID do seguidor iguar ao ID do usuario"))
		return
	}

	//abro o banco
	db, erro := banco.Conectar()
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
	}
	defer db.Close()

	repositorio := repositorios.NovoRepositorioUsuario(db)
	if err = repositorio.PararDeSeguir(usuarioID, usuarioIdToken); err != nil {
		respostas.Erro(w, http.StatusInternalServerError, err)
	}

	respostas.JSON(w, http.StatusNoContent, nil)
}

func BuscarSeguidores(w http.ResponseWriter, r *http.Request) {

	parametro := mux.Vars(r)
	usuarioID, erro := strconv.ParseUint(parametro["id"], 10, 64)
	if erro != nil {
		respostas.Erro(w, http.StatusBadRequest, erro)
		return
	}

	db, erro := banco.Conectar()
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
		return
	}
	defer db.Close()

	repositorio := repositorios.NovoRepositorioUsuario(db)
	seguidores, err := repositorio.BuscarSeguidor(usuarioID)
	if err != nil {
		respostas.Erro(w, http.StatusInternalServerError, err)
		return
	}

	respostas.JSON(w, http.StatusOK, seguidores)
}

func BuscarSeguindo(w http.ResponseWriter, r *http.Request) {

	parametro := mux.Vars(r)
	usuarioID, erro := strconv.ParseUint(parametro["id"], 10, 64)
	if erro != nil {
		respostas.Erro(w, http.StatusBadRequest, erro)
		return
	}

	db, erro := banco.Conectar()
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
		return
	}
	defer db.Close()

	repositorio := repositorios.NovoRepositorioUsuario(db)
	seguindo, err := repositorio.BuscarSeguindo(usuarioID)
	if err != nil {
		respostas.Erro(w, http.StatusInternalServerError, err)
		return
	}

	respostas.JSON(w, http.StatusOK, seguindo)
}

func AtualizarSenha(w http.ResponseWriter, r *http.Request) {

	usuarioIdToken, err := autenticacao.ExtrairUsuarioID(r)
	if err != nil {
		respostas.Erro(w, http.StatusUnauthorized, err)
		return
	}

	parametro := mux.Vars(r)
	usuarioID, erro := strconv.ParseUint(parametro["id"], 10, 64)
	if erro != nil {
		respostas.Erro(w, http.StatusBadRequest, erro)
		return
	}

	if usuarioID != usuarioIdToken {
		respostas.Erro(w, http.StatusForbidden, errors.New("Não é possíve atualizar um usario que não seja o seu."))
		return
	}

	corpoRequest, erro := ioutil.ReadAll(r.Body)

	var senha model.Senha // struct que possui campo para senha nova e atual

	if erro = json.Unmarshal(corpoRequest, &senha); erro != nil {
		respostas.Erro(w, http.StatusBadRequest, erro)
		return
	}

	db, erro := banco.Conectar()
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
		return
	}
	defer db.Close()

	repositorio := repositorios.NovoRepositorioUsuario(db)

	senhaSalvaNoBanco, erro := repositorio.BuscarSenha(usuarioID) //busca a senha atual do usuario
	if erro != nil {
		respostas.Erro(w, http.StatusInternalServerError, erro)
		return
	}

	if erro = seguranca.VerificarSenha(senhaSalvaNoBanco, senha.Atual); erro != nil { //verifica se a senha atual digitada é igual a senha do banco
		respostas.Erro(w, http.StatusUnauthorized, errors.New("A senha atual não condiz com a senha salva no banco"))
		return
	}

	senhaComHash, erro := seguranca.Hash(senha.Nova) // coloca Hash na nova senha
	if erro != nil {
		respostas.Erro(w, http.StatusBadRequest, erro)
		return
	}

	if erro = repositorio.AtualizarSenha(usuarioID, string(senhaComHash)); erro != nil {
		respostas.JSON(w, http.StatusInternalServerError, erro)
		return
	}

	respostas.JSON(w, http.StatusNoContent, nil)

}
