package main

import (
	"api/src/config"
	"api/src/router"
	"fmt"
	"log"
	"net/http"
)

func main() {
	config.Carregar()
	fmt.Println(config.StringConexaoBanco)
	fmt.Println("Porta: ", config.Porta)

	// fmt.Println(config.SecretKey)
	fmt.Println("Rodando Api")
	r := router.Gerar()
	enabledCORS(r)
	log.Fatal(http.ListenAndServe(fmt.Sprintf(":%d", config.Porta), r))
}
