package main

import (
	"fmt"
)

func main() {
	var a, b, c float64
	fmt.Scanf("%f %f %f", &a, &b, &c)
	t := (a * c) / 2
	circ := c * c * 3.14159
	trape := ((a + b) * c) / 2

	fmt.Printf("TRIANGULO: %.3f\n", t)
	fmt.Printf("CIRCULO: %.3f\n", circ)
	fmt.Printf("TRAPEZIO: %.3f\n", trape)
	fmt.Printf("QUADRADO: %.3f\n", b*b)
	fmt.Printf("RETANGULO: %.3f\n", a*b)
}
