package main

import "fmt"

func main() {
	var variavel1 = "var1"
	fmt.Printf(variavel1)

	var variavel2 = "var2"
	fmt.Println(variavel2)

	variavel1, variavel2 = variavel2, variavel1
	fmt.Println(variavel1, variavel2)
}
