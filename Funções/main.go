package main

import "fmt"

func somar(a int, b int) int {
	return a + b

}

func calculos(a, b int8) (int8, int8) {
	soma := a + b
	sub := a - b
	return soma, sub
}

func main() {
	soma := somar(2, 5)
	fmt.Println(soma)

	resulSoma, resulSub := calculos(2, 3)
	fmt.Println(resulSoma, resulSub)

	resulSoma2, _ := calculos(2, 3)
	fmt.Println(resulSoma2)
}
