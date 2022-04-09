package main

import (
	"fmt"
	"time"
)

func main() {

	go escrever("batata doce")
	escrever("Abacate")

}
func escrever(texto string) {
	for {
		fmt.Println(texto)
		time.Sleep(time.Second)

	}

}
