# onebox-endpoint


### Features
- /onebox/ is used for https://github.com/kokoro-ko/discourse-kokoro-box
- /humbebox/ is used for https://github.com/kokoro-ko/discourse-humble-box

MyAnimeList Endpoint based on https://jikan.moe/
- Humblebundle Links are cached to improve the loading times (1 week caching)

### Example for MyAnimeList
- Manga: https://api.kokoro-ko.de/onebox/onebox.php?api=mal&type=manga&name=Nana&id=28
- Anime: https://api.kokoro-ko.de/onebox/onebox.php?api=mal&type=anime&name=Mushishi&id=457


### Example for AnimeNewsNetwork
- Manga: https://api.kokoro-ko.de/onebox/onebox.php?api=ann&type=manga&id=2298
- Anime: https://api.kokoro-ko.de/onebox/onebox.php?api=ann&type=anime&id=877


### Example for Humblebundle
- Games: https://api.kokoro-ko.de/humblebox/index.php?type=games&urlCode=humble-indie-bundle-20
- Software: https://api.kokoro-ko.de/humblebox/index.php?type=software&urlCode=your-beats-acidized-software
- Books: https://api.kokoro-ko.de/humblebox/index.php?type=books&urlCode=linux-wiley-books
- Comics: COMING ? 

- Store: https://api.kokoro-ko.de/humblebox/index.php?type=store&urlCode=one-piece-world-seeker 
    + Discounts
    + Freebies
    + Fullprice


#### TO-DOs
- Humblebundle-Monthly