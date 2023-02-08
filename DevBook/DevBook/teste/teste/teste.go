package teste

import (
	"database/sql"
	"fmt"

	_ "github.com/go-sql-driver/mysql"
)

// var StringConexaoBanco = "root:/wise_clinicaucp?charset=utf8&parseTime=True&loc=Local"

func Connect() (*sql.DB, error) {
	//  aqui você substitui as variáveis pelas suas configs
	driverConfig := fmt.Sprintf("%s:%s@tcp(%s:%s)/%s", "root", "", "127.0.0.1", "3306", "wise_clinicaucp")
	connection, err := sql.Open("mysql", driverConfig)
	if err != nil {
		return nil, err
	}
	return connection, nil
}

// connection := Connect()
// connection.Close()
