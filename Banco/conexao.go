package main

import (
	"database/sql"
	"fmt"
	"log"

	_ "github.com/go-sql-driver/mysql"
)

func main() {
	// usuario:senha@nome_do_banco
	conexao := "root:Puma@2911@/test?charset=utf8&parseTime=True&loc=Local"
	db, erro := sql.Open("mysql", conexao)
	if erro != nil {
		log.Fatal(erro)
	}
	defer db.Close()

	if erro = db.Ping(); erro != nil {
		log.Fatal(erro)
	}
	fmt.Println("Conexap aberta")

	linhas, erro := db.Query("Select * from user")
	if erro != nil {
		log.Fatal(erro)
	}
	defer linhas.Close()
	fmt.Println(linhas)

}
