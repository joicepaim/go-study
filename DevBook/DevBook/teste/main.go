package main

import (
	"fmt"
	"log"
	"net/http"
	"teste/teste"
	"time"

	"github.com/gorilla/mux"
)

type Produtos struct {
	ID                 int64
	Nome               string
	Estoque_quantidade int64
	Estoque_fracao     int64
	Fracao             string
}

type Estoque_filial struct {
	filial_id         int64
	produto_id        int64
	status            int64
	created_at        time.Time
	filial_quantidade int64
	filial_fracao     int64
}

func main() {
	r := mux.NewRouter()
	r.HandleFunc("/rodar", Rodar)
	log.Fatal(http.ListenAndServe(":8081", r))

}
func Rodar(w http.ResponseWriter, r *http.Request) {
	produtos, err := getProdutos()
	if err != nil {
		fmt.Println(err)
	}

	// var e []Estoque_filial
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusOK)

	for _, p := range produtos {
		var estoque_filial Estoque_filial

		estoque_filial.filial_id = 1
		estoque_filial.produto_id = p.ID
		estoque_filial.filial_fracao = p.Estoque_fracao
		estoque_filial.filial_quantidade = p.Estoque_quantidade
		estoque_filial.status = 1
		estoque_filial.created_at = time.Now()

		ID, err := createEstoqueFilial(estoque_filial)
		if err != nil {
			log.Fatalln(err)
		}

		fmt.Println(ID)
		// e = append(e, estoque_filial)

	}

	// if err := json.NewEncoder(w).Encode(e); err != nil {
	// 	fmt.Println("erro no json")
	// 	log.Fatal(err)
	// }

	// if err := json.NewEncoder(w).Encode(produtos); err != nil {
	// 	fmt.Println("erro no json")
	// 	log.Fatal(err)
	// }
}

func getProdutos() ([]Produtos, error) {
	db, erro := teste.Connect()
	if erro != nil {
		return nil, erro
	}

	linhas, erro := db.Query(`SELECT id,nome,estoque_quantidade,estoque_fracao,fracao FROM produto`)
	if erro != nil {
		return nil, erro
	}

	var produtos []Produtos
	for linhas.Next() {
		var p Produtos

		if erro = linhas.Scan(
			&p.ID,
			&p.Nome,
			&p.Estoque_quantidade,
			&p.Estoque_fracao,
			&p.Fracao,
		); erro != nil {
			return nil, erro
		}

		produtos = append(produtos, p)
	}

	db.Close()

	return produtos, nil

}

func createEstoqueFilial(e Estoque_filial) (uint64, error) {
	db, erro := teste.Connect()
	if erro != nil {
		return 0, erro
	}

	statement, err := db.Prepare(
		"insert into estoque_filial (filial_id,produto_id,status,created_at,filial_quantidade,filial_fracao) values (?,?,?,?,?,?)")
	if err != nil {
		return 0, err
	}

	resultado, err := statement.Exec(e.filial_id, e.produto_id, e.status, e.created_at, e.filial_quantidade, e.filial_fracao)

	IDInserido, err := resultado.LastInsertId()
	if err != nil {
		return 0, err
	}
	db.Close()
	return uint64(IDInserido), nil
}
