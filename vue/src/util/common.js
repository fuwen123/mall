var EARTH_RADIUS = 6378137.0 // 单位M
var PI = Math.PI

function getRad (d) {
  return d * PI / 180.0
}

/**
 * caculate the great circle distance
 * @param {Object} lat1
 * @param {Object} lng1
 * @param {Object} lat2
 * @param {Object} lng2
 */

export function getDistance (lat1, lng1, lat2, lng2) {
  var f = getRad((lat1 + lat2) / 2)
  var g = getRad((lat1 - lat2) / 2)
  var l = getRad((lng1 - lng2) / 2)
  var sg = Math.sin(g)
  var sl = Math.sin(l)
  var sf = Math.sin(f)
  var s, c, w, r, d, h1, h2
  var a = EARTH_RADIUS
  var fl = 1 / 298.257
  sg = sg * sg
  sl = sl * sl
  sf = sf * sf
  s = sg * (1 - sl) + (1 - sf) * sl
  c = (1 - sg) * (1 - sl) + sf * sl
  w = Math.atan(Math.sqrt(s / c))
  r = Math.sqrt(s * c) / w
  d = 2 * w * a
  h1 = (3 * r - 1) / 2 / c
  h2 = (3 * r + 1) / 2 / s
  return d * (1 + fl * (h1 * sf * (1 - sg) - h2 * (1 - sf) * sg))
}
// 将一个对象转成QueryString
export function urlencode (data) {
  var _result = []
  for (var key in data) {
    var value = data[key]
    _result.push(key + '=' + value)
  }

  return _result.join('&')
}

// 加载js
let scriptLoaded={}
export function loadScript (code, url, callback) {
  if (typeof (scriptLoaded[code]) === 'undefined') {
    let script = document.createElement('script')
    script.src = url
    document.body.appendChild(script)

    script.onload = function () {
      scriptLoaded[code] = true
      callback()
    }
  } else {
    callback()
  }
}
