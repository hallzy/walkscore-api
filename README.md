# Walk Score API

The API that Walk Score provides is not easy to use because you have to provide
GPS coordinates in order for it to work, basically ignoring the address you
actually give it, which seems really silly.

This API just submits a request as if a user went to the site and typed an
address into the bar, and then it returns the following data to you, no
coordinates required:

- Walk Score
- Transit Score
- Bike Score
- The Walk Score URL with all the results

## Usage

Either add this file to your web server, or use the version I have at
`https://stmhall.ca/walkscore.php`.

There is 1 required param, and 1 optional param:

- `addr`: The address of the place you want to get the Walk Score for
- `variable` (optional): The name of a global variable to use to assign the
  results to (JSONP).

As mentioned, the `variable` param is only used for JSONP support, in case CORS
ends up being a pain in the butt.

### AJAX / Fetch

```
async () => {
    const response = await fetch('https://stmhall.ca/walkscore.php?addr=Rogers Arena Vancouver BC');
    const { walkScore, transitScore, bikeScore, url } = await response.json();
    console.log(walkScore, transitScore, bikeScore, url);
}
```

### JSONP

```
const script = document.createElement('script');
script.src = 'https://stmhall.ca/walkscore.php?variable=rogersArena&addr=Rogers Arena Vancouver BC';

script.addEventListener('load', () => {
    console.log(rogersArena);
});

document.body.appendChild(script);
```
