package main

import "fmt"

func escolha(numero int) string {
	switch numero {
	case 1:
		return "Um"
	case 2:
		return "Dois"
	case 3:
		return "Três"
	default:
		return "Num inválido"
	}
}

func escolha2(num1, num2 int) string {
	switch {
	case num1 == 10:
		return "Numero 1 = 10"
	case num2 == 5:
		return "Numero 2 = 5"
	default:
		return "Nenhuma"
	}
}
func main() {
	println("Estruturas de Controle")
	num := 10
	if outronum := num; outronum > 5 {
		fmt.Println(outronum)
	} else {
		fmt.Println("deidjei")
	}
	// fmt.Println(outronum) ñ é possível
	println("----------------------")
	num2 := escolha(3)
	fmt.Println(num2)

}
