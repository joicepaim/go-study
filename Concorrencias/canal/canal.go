package main

import (
	"fmt"
	"time"
)

func main() {
	canal := make(chan string)
	go escrever("Batata", canal)
	fmt.Println("Depois da função escrever começar ser executada")
	for {
		messagem, aberto := <-canal
		if !aberto {
			break
		}

		fmt.Println(messagem)
	}

	fmt.Println("Fim d	o programa")
}
func escrever(texto string, canal chan string) {
	for i := 0; i < 5; i++ {

		canal <- texto
		time.Sleep(time.Second)
	}
	close(canal)

}
