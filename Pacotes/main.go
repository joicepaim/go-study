package main

import (
	"fmt"
	"modulo/auxiliar"

	"github.com/badoux/checkmail"
)

func main() {

	fmt.Printf("Hello World\n")
	auxiliar.Escrever()
	erro := checkmail.ValidateFormat("joice@gmail.com")
	fmt.Println(erro)
}
