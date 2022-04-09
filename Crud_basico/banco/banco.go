package banco

import (
	"database/sql"
	"fmt"

	_ "github.com/go-sql-driver/mysql" // mysql
)

func Conectar() (*sql.DB, error) {
	conexao := "root:Puma@2911@/test?charset=utf8&parseTime=True&loc=Local"
	db, erro := sql.Open("mysql", conexao)
	if erro != nil {
		return nil, erro
	}

	if erro = db.Ping(); erro != nil {
		return nil, erro
	}

	fmt.Println("Conexap aberta")
	return db, nil
}
