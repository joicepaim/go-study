package main

import "fmt"

func recuperarExecucao() {
	// println("Tentativa")
	if r := recover(); r != nil {
		fmt.Println("Execução recuperada")
	}

}
func media(n1, n2 float64) bool {
	defer recuperarExecucao()
	media := (n1 + n2) / 2
	if media > 6 {
		return true
	} else if media < 6 {
		return false
	} else {
		panic("A média é exatamente 6!!")
	}
}
func main() {
	fmt.Println(media(5, 7))
	println("Fim da execução")
}
