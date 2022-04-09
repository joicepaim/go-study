package main

import (
	"fmt"
	"log"
	"os"

	"linha-de-comando/app"
)

func main() {
	fmt.Println("ok")
	aplicacao := app.Gerar()
	if erro := aplicacao.Run(os.Args); erro != nil {
		log.Fatal(erro)
	}
}
