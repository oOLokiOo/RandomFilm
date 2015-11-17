package main

import (
	"net/http"
	"fmt"
	"encoding/xml"
	"io/ioutil"
	"math/rand"
	"strconv"
	//"strings"
	"encoding/json"
	"net/url"
	"image"
	"image/draw"
	_ "image/jpeg"

	"github.com/google/gxui"
	"github.com/google/gxui/math"
	"github.com/google/gxui/gxfont"
	"github.com/google/gxui/drivers/gl"
	"github.com/google/gxui/samples/flags"
)


type Movies struct {
	XMLName 	xml.Name 	`xml:"movies"`
	FilmsList 	[]Film 		`xml:"film"`
}

type Film struct {
	Ru 			string 	`xml:"ru"`
	En 			string 	`xml:"en"`
	Year 		string 	`xml:"year"`
	Kinopoisk 	string 	`xml:"kinopoisk"`
	Imdb 		string 	`xml:"imdb"`
}

type JsonMap struct {
	ResponseData struct {
		Results []struct {
				Url 		string `json:"url"`
				VisibleUrl 	string `json:"visibleUrl"`
			} `json:"results"`
	} `json:"responseData"`
}

var (
	APP_TITLE 			string = "Random movie that you would like to revise (c) Script by oOLokiOo"
	WINDOW_WIDTH 		int = 800
	WINDOW_HEIGHT 		int = 600
	H1_FONT_SIZE 		int = 30
	GOOGLE_IMAGES_URL 	string 	= "http://ajax.googleapis.com/ajax/services/search/images?v=1.0&imgsz=large&rsz=8&q="
	EN_SEARCH_PREFIX 	string 	= "film poster"
	RU_SEARCH_PREFIX 	string 	= "фильм постер"
	BLOCKED_RESOURCES = []string {
		"www.impawards.com",
		"en.wikipedia.org",
	}
	XML_PATH 			string 	= "../../../users/1/films.xml"
	CSS_ROOT 			string 	= "../../../css/"
)


func catch(err error) {
	if err != nil {
		fmt.Println("\n\n--- --- ---\nERROR: ", err)
		fmt.Println("--- --- ---\n")
		panic(err)

		return
	}
}

func stringInSlice(str string, list []string) bool {
	for _, listVal := range list {
		if listVal == str {
			return true
		}
	}

    return false
}

func filterFromBlockedResources(arr JsonMap) (good_url string) {
	for _, row := range arr.ResponseData.Results {
        if stringInSlice(row.VisibleUrl, BLOCKED_RESOURCES) == false {
            good_url = row.Url
            return good_url
        }
	}

	return good_url
}

func getFilm(driver gxui.Driver, h1Title gxui.Label, KinopoiskLabel gxui.Label, ImdbLabel gxui.Label, img gxui.Image) {
	m := Movies{}

	// TODO: cache file in memory
	xmlFile, _ := ioutil.ReadFile(XML_PATH)
	err := xml.Unmarshal(xmlFile, &m)
	catch(err)
 
	rand := rand.Intn(len(m.FilmsList));
	random_movie := m.FilmsList[rand]

	search_movie_title := ""
	if random_movie.En != "" { search_movie_title += random_movie.En + " " }
	if random_movie.Ru != "" { search_movie_title += random_movie.Ru + " " }
	if random_movie.Year != "" { search_movie_title += random_movie.Year + " " }
	search_movie_title += EN_SEARCH_PREFIX

	if search_movie_title != "" {
		resp, err := http.Get(GOOGLE_IMAGES_URL + url.QueryEscape(search_movie_title))
		catch(err)

		if (resp.StatusCode == 200) {
			defer resp.Body.Close()
			jsonData, err := ioutil.ReadAll(resp.Body)
			catch(err)

			var jsonMap JsonMap
			
			err = json.Unmarshal([]byte(jsonData), &jsonMap)
			catch(err)

			ImageUrl := filterFromBlockedResources(jsonMap)
			f, err := http.Get(ImageUrl)
			catch(err)
			source, _, err := image.Decode(f.Body)
			catch(err)
			rgba := image.NewRGBA(source.Bounds())
			draw.Draw(rgba, source.Bounds(), source, image.ZP, draw.Src)
			texture := driver.CreateTexture(rgba, 1)
			img.SetTexture(texture)
		}

		title := ""
		if random_movie.Ru != "" 	{ title += random_movie.Ru + " | " }
		if random_movie.En != "" 	{ title += random_movie.En + " | " }
		if random_movie.Year != "" 	{ title += random_movie.Year }

		if (title[len(title)-2:] == "| ") { title = title[:len(title)-2] }
		h1Title.SetText(title)
		//p.TitleForSearch = strings.Replace(title, " | ", " ", -1) // TODO: TitleForSearch == search_movie_title ? replace it!

		KinopoiskLabel.SetText(random_movie.Kinopoisk)
		ImdbLabel.SetText(random_movie.Imdb)
	} else {
		h1Title.SetText("Movie [" + strconv.Itoa(rand) + "] - not found!")
	}
}

func appMain(driver gxui.Driver) {
	theme := flags.CreateTheme(driver)

	font, err := driver.CreateFont(gxfont.Default, H1_FONT_SIZE)
	catch(err)

	window := theme.CreateWindow(WINDOW_WIDTH, WINDOW_HEIGHT, APP_TITLE)
	window.SetBackgroundBrush(gxui.CreateBrush(gxui.White))
	window.SetScale(flags.DefaultScaleFactor)
	window.SetPadding(math.Spacing{L: 10, R: 10, T: 10, B: 10})

	layout := theme.CreateLinearLayout()
	layout.SetSizeMode(gxui.Fill)
	layout.SetHorizontalAlignment(gxui.AlignCenter)
	layout.HorizontalAlignment().AlignCenter()

	fullscreenButton := theme.CreateButton()
	fullscreenButton.SetText("Make Fullscreen")
	fullscreenButton.OnClick(func(ev gxui.MouseEvent) {
		fullscreen := !window.Fullscreen()
		window.SetFullscreen(fullscreen)

		if fullscreen {
			fullscreenButton.SetText("Make Windowed")
		} else {
			fullscreenButton.SetText("Make Fullscreen")
		}
	})

	h1Title := theme.CreateLabel()
	h1Title.SetFont(font)
	h1Title.SetColor(gxui.Color{R: 0, G: 0, B: 0, A: 1,})

	KinopoiskLabel := theme.CreateLabel()
	KinopoiskLabel.SetFont(font)
	KinopoiskLabel.SetColor(gxui.Color{R: 0, G: 0, B: 0, A: 1,})

	ImdbLabel := theme.CreateLabel()
	ImdbLabel.SetFont(font)
	ImdbLabel.SetColor(gxui.Color{R: 0, G: 0, B: 0, A: 1,})

	// https://github.com/google/gxui/blob/master/samples/image_viewer/main.go
	img := theme.CreateImage()

	getFilmButton := theme.CreateButton()
	getFilmButton.SetText("Get Film!")
	getFilmButton.OnClick(func(ev gxui.MouseEvent) {
		getFilm(driver, h1Title, KinopoiskLabel, ImdbLabel, img)
	})

	getFilm(driver, h1Title, KinopoiskLabel, ImdbLabel, img)

	layout.AddChild(fullscreenButton)
	layout.AddChild(h1Title)
	layout.AddChild(KinopoiskLabel)
	layout.AddChild(ImdbLabel)
	layout.AddChild(getFilmButton)
	layout.AddChild(img)
	window.AddChild(layout)

	window.OnClose(driver.Terminate)
}


func main() {
	gl.StartDriver(appMain)
}