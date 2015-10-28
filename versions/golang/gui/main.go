package main

import (
	"fmt"

	"github.com/google/gxui"
	"github.com/google/gxui/drivers/gl"
	"github.com/google/gxui/gxfont"
	"github.com/google/gxui/samples/flags"
)


var (
	APP_TITLE 		string = "Random movie that you would like to revise (c) Script by oOLokiOo"
	WINDOW_WIDTH 	int = 800
	WINDOW_HEIGHT 	int = 600
)


func catch(err error) {
	if err != nil {
		fmt.Println("\n\n--- --- ---\nERROR: ", err)
		fmt.Println("--- --- ---\n")
		panic(err)

		return
	}
}


func appMain(driver gxui.Driver) {
	theme := flags.CreateTheme(driver)

	font, err := driver.CreateFont(gxfont.Default, 75)
	catch(err)

	window := theme.CreateWindow(WINDOW_WIDTH, WINDOW_HEIGHT, APP_TITLE)
	window.SetBackgroundBrush(gxui.CreateBrush(gxui.Gray50))

	label := theme.CreateLabel()
	label.SetFont(font)
	label.SetText("just test")
	window.AddChild(label)

	window.OnClose(driver.Terminate)
}


func main() {
	gl.StartDriver(appMain)
}