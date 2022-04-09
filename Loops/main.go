package main

import (
	"fmt"
)

func main() {
	// i := 0
	// for i < 10 {
	// 	i++
	// 	println(i)
	// 	time.Sleep(time.Second)
	// }

	// for j := 0; j < 10; j++ {
	// 	println(j)
	// }

	nomes := [3]string{"Julia", "Ana", "João"}
	for indice, nome := range nomes {
		fmt.Println(indice, " ", nome)
	}

	for _, nome := range nomes { //por padrão o for range traz primeiro o indice e depois o nome
		println(nome)
	}

	for _, nome := range "PALAVRA" {
		println(nome)
	}

	for _, nome := range "PALAVRA" {
		println(string(nome))
	}

	// usuario := map[string]string{"nome": "Joice", "Sobrenome": "Paim"}
	type Test struct {
		id   int32
		nome string
	}

	var teste = []Test{
		{
			1, "ana",
		},
		{
			2, "julai",
		},
	}
	for chave, valor := range teste {
		fmt.Println(chave, valor.nome)
	}

	// for {
	// 	print("Loop infinito")
	// 	time.Sleep(time.Second)
	// }
}
