package main

import (
	"fmt"
	"math"
)

type retangulo struct {
	c float64
	l float64
}

func (r retangulo) area() float64 {
	return r.c * r.l
}

type circulo struct {
	raio float64
}

func (c circulo) area() float64 {
	return math.Pi * c.raio * c.raio
}

type forma interface {
	area() float64
}

func escreverArea(f forma) {
	fmt.Printf("A área da forma é %.2f\n", f.area())

}

func main() {
	r := retangulo{10, 15}
	escreverArea(r)
	c := circulo{100}
	escreverArea(c)
}
