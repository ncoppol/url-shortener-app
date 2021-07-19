<!DOCTYPE html>
<html lang="en">
    <head>
        <title>URL Shortener App</title>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>

    <center><h1><b>URL Shortener App</b></h1></center>

    <br />
    <br />

    <body>
        <main>

            <section class="py-5 container">

                <?= \Config\Services::validation()->listErrors() ?>

                <div id="div1" class="row">
                    <form action="/api/add" method="post" id="url-add-form">
                        <center><h2><?= esc($title) ?></h2></center>
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label for="url">Full URL</label>
                            <input type="url" class="form-control" id="url" name="url" placeholder="Enter Full URL here (ex. https://ltvco.com)" required>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="nsfw" name="nsfw">
                            <label class="form-check-label" for="nsfw">NSFW Link?</label>
                        </div>
                        <button class="w-100 btn btn-lg btn-primary" type="submit">Generate Short URL</button>
                    </form>
                    <div id="result" class="row bg-success text-white">
                    </div>
                    <div id="error" class="row bg-danger text-white">
                    </div>
                </div>

                
                
            </section>

            <section class="py-5 text-center container content-info">
                <h2>Rankings (Top 100)</h2>
                <div class="container paddings-mini">
                    <div class="col-lg-12">
                        <table class="table table-striped table-responsive table-hover result-point">
                            <thead class="point-table-head">
                                <tr>
                                    <th class="text-left">Ranking</th>
                                    <th class="text-center">Short URL</th>
                                    <th class="text-center">Full URL</th>
                                    <th class="text-center">Clicks</th>
                                </tr>
                            </thead>
                            <tbody id="rankings" class="text-center">
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script>
            document.body.onload = generateRankings

            async function generateRankings() {
                let url = '/api/top'
            
                let fetchOptions = {
                    method: "GET"
                }
                let result = await fetch(url, fetchOptions).then(response => {
                    response.json().then(json => {
                        let rankings = document.getElementById('rankings')
                        for (let i=0; i < json.length; i++) {
                            let row = document.createElement("tr")
                            let number = i + 1
                            let rank = document.createElement("td")
                            rank.innerHTML = number + "."
                            row.appendChild(rank)
                            let short = document.createElement("td")
                            short.innerHTML = json[i].short_url
                            row.appendChild(short)
                            let full = document.createElement("td")
                            full.innerHTML = json[i].full_url
                            row.appendChild(full)
                            let clicks = document.createElement("td")
                            clicks.innerHTML = json[i].clicks
                            row.appendChild(clicks)
                            //div.innerHTML = number + ".  " + "Short: " + json[i].short_url + "  Full: " + json[i].full_url + "  Clicks: " + json[i].clicks;
                            rankings.appendChild(row)
                        }
                    })
                })
            }

            let urlForm = document.getElementById('url-add-form')
            urlForm.addEventListener('submit', sendPostCall)

            async function sendPostCall(event) {
                event.preventDefault()
                let form = event.currentTarget
                let url = form.action
            
                let formData = new FormData(form)
                let fetchOptions = {
                    method: "POST",
                    body: formData
                }
                await fetch(url, fetchOptions).then(response => {
                    response.json().then(json => {
                        let newDiv = document.createElement("div")
                        if (json.short_url) {
                            let shortUrl = json.short_url
                            newDiv.classList.add('row', 'bg-success', 'text-white')
                            let newContent = document.createTextNode("Your new short URL is: ")
                            newDiv.appendChild(newContent)
                            let newLink = document.createElement("a")
                            let url = document.createTextNode(shortUrl)
                            newLink.href = shortUrl
                            newLink.appendChild(url)
                            newDiv.appendChild(newLink)
                            let currentDiv = document.getElementById("result")
                            parentNode = document.getElementById("div1")
                            parentNode.insertBefore(newDiv, currentDiv)
                        }
                        else if (json.error) {
                            let error = json.error
                            let message = 'Invalid URL!'
                            newDiv.classList.add('row', 'bg-danger', 'text-white')
                            if (json.messages.error) message = json.messages.error
                            let newContent = document.createTextNode(message)
                            newDiv.appendChild(newContent)
                            let currentDiv = document.getElementById("error")
                            parentNode = document.getElementById("div1")
                            parentNode.insertBefore(newDiv, currentDiv)
                        }
                    })
                })
            }
        </script>
    </body>

</html>