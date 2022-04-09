package main

import (
	"bufio"
	"fmt"
	"net"
	"os"
	"strconv"
	"strings"
)

const (
	message       = "01+REON+00+30]4]NAO ENCONTRADO]"
	StopCharacter = "\r\n\r\n"
)

func SocketClient(ip string, port int) {
	addr := strings.Join([]string{ip, strconv.Itoa(port)}, ":")

	conexao, erro1 := net.Dial("tcp", addr)
	if erro1 != nil {
		fmt.Println(erro1)
		os.Exit(3)
	}

	for {

		// ouvindo a resposta do servidor (eco)
		mensagem, err3 := bufio.NewReader(conexao).ReadString('\n')
		if err3 != nil {
			fmt.Println(err3)
			os.Exit(3)
		}
		// escrevendo a resposta do servidor no terminal
		fmt.Print("Resposta do servidor: " + mensagem)
	}
}

func main() {

	var (
		ip   = "10.10.11.175"
		port = 3000
	)

	SocketClient(ip, port)
}
