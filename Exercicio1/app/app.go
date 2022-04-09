package app

import (
	"github.com/urfave/cli"
)

func Gerar() *cli.App {
	app := NewApp()
	app.Name = "Nome"
	return app
}
