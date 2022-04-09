package main

import "fmt"

type usuario struct {
	nome     string
	idade    int8
	endereco endereco
}
type endereco struct {
	rua    string
	numero int16
	// bairro string
	// cep    int
}

type pessoa struct {
	nome   string
	rg     int
	altura float32
	idade  int8
}

type estudante struct {
	ra int
	pessoa
}

func main() {

	var u usuario
	u.idade = 23
	u.nome = "Ana"

	fmt.Println(u)
	endereco := endereco{"R. dos bobos", 0}

	u2 := usuario{"Anaa", 21, endereco}

	fmt.Println("Usuario: ", u2)

	p1 := pessoa{"Joice", 2333, 1.68, 18}
	fmt.Println(p1)

	e1 := estudante{12222, p1}
	fmt.Println(e1)

}
