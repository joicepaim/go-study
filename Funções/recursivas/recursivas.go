package main

func fibonacci(posicao uint) (uint uint) {
	if posicao <= 1 {
		return posicao
	}
	return fibonacci(posicao-1) + fibonacci(posicao-2)
}
func main() {
	posicao := fibonacci(10)
	println(posicao)
}
