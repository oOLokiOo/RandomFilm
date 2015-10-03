var films = [
	{
		"ru": "test",
		"en": "тест",
		"year": 1234,
		"kinopoisk": "http://www.kinopoisk.ru/",
		"imdb": "http://www.imdb.com/"
	},
	{
		"ru": "Мстители",
		"en": "The Avengers",
		"year": 2012,
		"kinopoisk": "http://www.kinopoisk.ru/film/263531/",
		"imdb": "http://www.imdb.com/title/tt0848228/"
	},
	{
		"ru": "Халк",
		"en": "Hulk",
		"year": 2003,
		"kinopoisk": "http://www.kinopoisk.ru/film/7962/"
	},
	{
		"ru": "Третий лишний",
		"en": "Ted",
		"year": 2003,
		"kinopoisk": "http://www.kinopoisk.ru/film/507817/"
	},
	{
		"ru": "Игра",
		"en": "The Game",
		"year": 1997,
		"imdb": "http://www.imdb.com/title/tt0119174/"
	},
	{
		"ru": "Телепорт",
		"en": "Jumper",
		"year": 2008,
		"kinopoisk": "http://www.kinopoisk.ru/film/220589/"
	},
	{
		"ru": "Угнать за 60 секунд",
		"en": "Gone in Sixty Seconds",
		"year": 2000,
		"imdb": "http://www.imdb.com/title/tt0187078/"
	},
	{
		"ru": "Пророк",
		"en": "Next",
		"year": 2007
	},
	{
		"ru": "Город ангелов",
		"en": "City of Angels",
		"year": 1998
	},
	{
		"ru": "Мачо и ботан",
		"en": "21 Jump Street",
		"year": 2012
	},
	{
		"ru": "Рок-волна",
		"en": "The Boat That Rocked",
		"year": 2009
	},
	{
		"ru": "Незваные гости",
		"en": "Wedding Crashers",
		"year": 2005
	},
	{
		"ru": "Начало",
		"en": "Inception",
		"year": 2010
	},
	{
		"ru": "Социальная сеть",
		"en": "The Social Network",
		"year": 2010
	},
	{
		"ru": "Эквилибриум",
		"en": "Equilibrium",
		"year": 2002
	},
	{
		"ru": "Престиж",
		"en": "The Prestige",
		"year": 2002
	},
	{
		"ru": "Треугольник",
		"en": "Triangle",
		"year": 2009
	},
	{
		"ru": "Королева проклятых",
		"en": "Queen of the Damned",
		"year": 2002
	},
	{
		"ru": "Мистер и миссис Смит",
		"en": "Mr. & Mrs. Smith",
		"year": 2005
	},
	{
		"ru": "Послезавтра",
		"en": "The Day After Tomorrow",
		"year": 2004
	},
	{
		"ru": "1408",
		"en": "1408",
		"year": 2007
	},
	{
		"ru": "2012",
		"en": "2012",
		"year": 2009
	},
	{
		"ru": "Война миров",
		"en": "War of the Worlds",
		"year": 2005
	},
	{
		"ru": "Двухсотлетний человек",
		"en": "Bicentennial Man",
		"year": 1999
	},
	{
		"ru": "Искусственный разум",
		"en": "Artificial Intelligence: AI",
		"year": 2001
	},
	{
		"ru": "Аватар",
		"en": "Avatar",
		"year": 2009
	},
	{
		"ru": "Казанова",
		"en": "Casanova",
		"year": 2005
	},
	{
		"ru": "Я, робот",
		"en": "I, Robot",
		"year": 2004
	},
	{
		"ru": "Суррогаты",
		"en": "Surrogates",
		"year": 2009
	},
	{
		"ru": "Остаться в живых",
		"en": "Stay Alive",
		"year": 2006
	},
	{
		"ru": "Особо опасен",
		"en": "Wanted",
		"year": 2008
	},
	{
		"ru": "Живая сталь",
		"en": "Real Steel",
		"year": 2011
	},
	{
		"ru": "Тринадцатый этаж",
		"en": "The Thirteenth Floor",
		"year": 1999
	},
	{
		"ru": "Время",
		"en": "In Time",
		"year": 2011
	},
	{
		"ru": "Пастырь",
		"en": "Priest",
		"year": 2011
	},
	{
		"ru": "Исходный код",
		"en": "Source Code",
		"year": 2011
	},
	{
		"ru": "Области тьмы",
		"en": "Limitless",
		"year": 2011
	},
	{
		"ru": "Меняющие реальность",
		"en": "The Adjustment Bureau",
		"year": 2011
	},
	{
		"ru": "Ученик чародея",
		"en": "The Sorcerer's Apprentice",
		"year": 2010
	},
	{
		"ru": "Телефонная будка",
		"en": "Phone Booth",
		"year": 2002
	},
	{
		"ru": "Константин: Повелитель тьмы",
		"en": "Constantine",
		"year": 2005
	},
	{
		"ru": "Зеленая миля",
		"en": "The Green Mile",
		"year": 1999
	},
	{
		"ru": "Достучаться до небес",
		"en": "Knockin' on Heaven's Door",
		"year": 1997
	},
	{
		"ru": "Голодные игры",
		"en": "The Hunger Games",
		"year": 2012
	},
	{
		"ru": "Хэнкок",
		"en": "Hancock",
		"year": 2008
	},
	{
		"ru": "Экстремалы",
		"en": "Extreme Ops",
		"year": 2002
	},
	{
		"ru": "Лица в толпе",
		"en": "Faces in the Crowd",
		"year": 2011
	},
	{
		"ru": "Особое мнение",
		"en": "Minority Report",
		"year": 2002
	},
	{
		"ru": "Охотники за разумом",
		"en": "Mindhunters",
		"year": 2004
	},
	{
		"ru": "Ловец снов",
		"en": "Dreamcatcher",
		"year": 2003
	},
	{
		"ru": "Остров",
		"en": "The Island",
		"year": 2005
	},
	{
		"ru": "Власть огня",
		"en": "Reign of Fire",
		"year": 2002
	},
	{
		"ru": "Книга Илая",
		"en": "The Book of Eli",
		"year": 2009
	},
	{
		"ru": "Дежа Вю",
		"en": "Deja vu",
		"year": 2006
	},
	{
		"ru": "Покорители волн",
		"en": "Chasing Mavericks",
		"year": 2012,
		"kinopoisk": "http://www.kinopoisk.ru/film/546244/"
	},
	{
		"ru": "Смертельная гонка",
		"en": "Death Race",
		"year": 2008,
		"kinopoisk": "http://www.kinopoisk.ru/film/102136/"
	},
	{
		"ru": "Вспомнить всё",
		"en": "Total Recall",
		"year": 2012,
		"kinopoisk": "http://www.kinopoisk.ru/film/453374/"
	},
	{
		"ru": "В первый раз",
		"en": "The First Time",
		"year": 2012,
		"kinopoisk": "http://www.kinopoisk.ru/film/572376/"
	},
];