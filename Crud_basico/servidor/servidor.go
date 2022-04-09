package servidor

import (
	"crud/banco"
	"encoding/json"
	"fmt"
	"io/ioutil"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"
)

type user struct {
	Id       uint   `json:"id"`
	Nome     string `json:"username"`
	Password string `json:"password"`
}

func CriarUser(w http.ResponseWriter, r *http.Request) {
	corpoRequisicao, erro := ioutil.ReadAll(r.Body) // utilizado para ler as requisições
	if erro != nil {
		w.Write([]byte("Falha ao Ler a requisição"))
		return
	}

	var usuario user

	//passando o corpo da requisição no endereço de memoria do struct
	if erro = json.Unmarshal(corpoRequisicao, &usuario); erro != nil {
		w.Write([]byte("Falha ao  converter user para struct"))
		return
	}

	fmt.Println(usuario)

	// conectando o banco
	db, erro := banco.Conectar()
	if erro != nil {
		w.Write([]byte("Erro ao conectar banco de dados! "))
	}
	defer db.Close()

	//PREPARE STATEMENT : cria um comando de inserção muito utilizado para evitar ataques SQL injection
	statement, erro := db.Prepare("insert into user(username,password) values(?,?)") // não passa diretamente os valores que serão manipulados
	if erro != nil {
		w.Write([]byte("Erro ao criar statement!"))
		return
	}
	defer statement.Close()

	insercao, erro := statement.Exec(usuario.Nome, usuario.Password)
	if erro != nil {
		w.Write([]byte("Erro ao executar statement!"))
		return
	}

	//devolve id do user inserido
	idInserido, erro := insercao.LastInsertId()
	if erro != nil {
		w.Write([]byte("Erro ao obter o id inserido!"))
		return
	}

	w.Write([]byte(fmt.Sprintf("Usuario inserido com sucesso: id %d", idInserido)))

}

func ConsultarUsers(w http.ResponseWriter, r *http.Request) {

	// conectando o banco
	db, erro := banco.Conectar()
	if erro != nil {
		w.Write([]byte("Erro ao conectar banco de dados! "))
		return
	}
	defer db.Close()

	//consulta
	linhas, erro := db.Query("select * from user")
	if erro != nil {
		w.Write([]byte("Erro ao buscar os usuarios"))
		return
	}
	defer linhas.Close()

	//cria um slice de usuarios para receber as linhas
	var usuarios []user

	for linhas.Next() { // passa pelas linhas da consulta
		var usuario user
		if erro := linhas.Scan(&usuario.Id, &usuario.Nome, &usuario.Password); erro != nil { // scanea uma linha da consulta em um struct usuario
			w.Write([]byte("Erro ao Scanear um usuario"))
			return
		}
		fmt.Println(usuario)

		usuarios = append(usuarios, usuario) // isere um item usuario no slice de usuarios

		fmt.Println(usuarios)
	}

	// w.WriterHeader(http.StatusOk)

	if erro := json.NewEncoder(w).Encode(usuarios); erro != nil {
		w.Write([]byte("Erro ao transformar em json o slice de usuarios"))
		return
	}

}

func ConsultarUser(w http.ResponseWriter, r *http.Request) {

	parametro := mux.Vars(r) // pega o parametro que retorna como string

	ID, erro := strconv.ParseUint(parametro["id"], 10, 32) // transforma parametro em int base 10, 32 bits
	if erro != nil {
		w.Write([]byte("Erro ao obter o id do parametro!"))
		return
	}

	// conectando o banco
	db, erro := banco.Conectar()
	if erro != nil {
		w.Write([]byte("Erro ao conectar banco de dados! "))
	}
	defer db.Close()

	//consulta
	linha, erro := db.Query("select * from user where id = ?", ID)
	if erro != nil {
		w.Write([]byte("Erro ao buscar o usuario"))
		return
	}
	defer linha.Close()

	var usuario user
	if linha.Next() {
		if erro := linha.Scan(&usuario.Id, &usuario.Nome, &usuario.Password); erro != nil {
			w.Write([]byte("Erro ao scanear o usuario"))
			return
		}

	}

	if erro := json.NewEncoder(w).Encode(usuario); erro != nil { //outra forma de transformar struct em json
		w.Write([]byte("Erro ao transformar em json o slice de usuarios"))
		return
	}

}

func AtualizarUser(w http.ResponseWriter, r *http.Request) {

	parametro := mux.Vars(r) // pega o parametro que retorna como string

	ID, erro := strconv.ParseUint(parametro["id"], 10, 32) // transforma parametro em int base 10, 32 bits
	if erro != nil {
		w.Write([]byte("Erro ao obter o id do parametro!"))
		return
	}

	corpoRequisicao, erro := ioutil.ReadAll(r.Body) // utilizado para ler as requisições
	if erro != nil {
		w.Write([]byte("Falha ao Ler a requisição"))
		return
	}

	var usuario user

	//passando o corpo da requisição no endereço de memoria do struct
	if erro = json.Unmarshal(corpoRequisicao, &usuario); erro != nil {
		w.Write([]byte("Falha ao  converter user para struct"))
		return
	}

	fmt.Println(usuario)

	// conectando o banco
	db, erro := banco.Conectar()
	if erro != nil {
		w.Write([]byte("Erro ao conectar banco de dados! "))
	}
	defer db.Close()

	//UPDATE com stament
	statement, erro := db.Prepare("update user set username=?, password=? where id=?") // não passa diretamente os valores que serão manipulados
	if erro != nil {
		w.Write([]byte("Erro ao criar statement!"))
		return
	}
	defer statement.Close()

	if _, erro := statement.Exec(usuario.Nome, usuario.Password, ID); erro != nil {
		w.Write([]byte("Erro ao executar statement!"))
		return
	}
	w.WriteHeader(http.StatusNoContent)

}

func DeletarUser(w http.ResponseWriter, r *http.Request) {

	parametro := mux.Vars(r) // pega o parametro que retorna como string

	ID, erro := strconv.ParseUint(parametro["id"], 10, 32) // transforma parametro em int base 10, 32 bits
	if erro != nil {
		w.Write([]byte("Erro ao obter o id do parametro!"))
		return
	}

	// conectando o banco
	db, erro := banco.Conectar()
	if erro != nil {
		w.Write([]byte("Erro ao conectar banco de dados! "))
	}
	defer db.Close()

	statement, erro := db.Prepare("Delete from user where id = ?") // não passa diretamente os valores que serão manipulados
	if erro != nil {
		w.Write([]byte("Erro ao criar statement!"))
		return
	}
	defer statement.Close()

	if _, erro := statement.Exec(ID); erro != nil {
		w.Write([]byte("Erro ao executar statement!"))
		return
	}
	w.Write([]byte("User deletado com sucesso"))

}
