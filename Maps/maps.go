package main

import "fmt"

func main() {
	usuario := map[string]string{
		"nome":      "Joice",
		"sobrenome": "Paim",
	}

	fmt.Println(usuario)
	fmt.Println(usuario["nome"])

	usuario2 := map[string]map[string]string{
		"nome": {
			"Primeiro": "Joice",
			"Apelido":  "Paim",
		},
	}
	fmt.Println(usuario2["nome"])

}
