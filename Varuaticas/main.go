package main

import (
	"fmt"
)

func calculos(numeros ...int) {
	fmt.Println(numeros)
}

func main() {
	calculos(1, 2, 3, 4, 4, 5, 6)
}
