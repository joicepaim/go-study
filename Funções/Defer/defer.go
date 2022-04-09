package main

func funcao1() {
	print("Função 1\n")
}
func funcao2() {
	println("Funcao 2")
}

func main() {
	defer funcao1() // adia a execução de um pedaço de código até o último momento possível
	funcao2()
}
