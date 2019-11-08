import { requestRaw } from './network'

let navigator_id

export function getPosition (callback, start) {
  if (navigator.geolocation) {
    var tmid = window.setTimeout(function () {
      let res = { code: 10001, message: '定位超时', result: '' }
      callback(res)
    }, 1000 * 15)
    var func = start ? 'getCurrentPosition' : 'watchPosition'

    let temp = navigator.geolocation[func](
      function (position) {
        window.clearTimeout(tmid)
        let res = {
          code: 10000,
          message: '',
          result: { lng: position.coords.longitude, lat: position.coords.latitude }
        }
        callback(res)
      },
      function (error) {
        window.clearTimeout(tmid)
        let message = ''
        switch (error.code) {
          case error.PERMISSION_DENIED:
            message = '定位请求拒绝'
            break
          case error.POSITION_UNAVAILABLE:
            message = '定位获取失败'
            break
          case error.TIMEOUT:
            message = '定位超时'
            break
          default:
            message = '未知错误'
            break
        }
        let res = { code: 10001, message: message, result: '' }
        callback(res)
      }
    )
    if (!start) {
      navigator_id = temp
    }
  } else {
    let res = { code: 10001, message: '定位不支持', result: '' }
    callback(res)
  }
}

export function addMap (container, callleft, callright, calltop, calldown) {
  let _bmap = new BMap.Map(container)

  var zoomCtrl = new BMap.ZoomControl({ anchor: BMAP_ANCHOR_BOTTOM_RIGHT, offset: new BMap.Size(20, 20) })
  _bmap.addControl(zoomCtrl)

  // 创建控件
  function PanLeftControl () {
    this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT
    this.defaultOffset = new BMap.Size(10, 10)
  }
  PanLeftControl.prototype = new BMap.Control()
  PanLeftControl.prototype.initialize = function (map) {
    var div = document.createElement('div')
    div.className = 'bmap-pan-control pan-left iconfont icon-arrow-down'
    div.onclick = function (e) {
      map.panBy(100, 0)
      if (typeof callleft === 'function') {
        callleft()
      }
    }
    map.getContainer().appendChild(div)
    return div
  }
  var panLeftCtrl = new PanLeftControl()
  // 添加到地图当中
  _bmap.addControl(panLeftCtrl)

  // 创建控件
  function PanRightControl () {
    this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT
    this.defaultOffset = new BMap.Size(10, 10)
  }
  PanRightControl.prototype = new BMap.Control()
  PanRightControl.prototype.initialize = function (map) {
    var div = document.createElement('div')
    div.className = 'bmap-pan-control pan-right iconfont icon-arrow-down'
    div.onclick = function (e) {
      map.panBy(-100, 0)
      if (typeof callright === 'function') {
        callright()
      }
    }
    map.getContainer().appendChild(div)
    return div
  }
  var panRightCtrl = new PanRightControl()
  // 添加到地图当中
  _bmap.addControl(panRightCtrl)

  // 创建控件
  function PanTopControl () {
    this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT
    this.defaultOffset = new BMap.Size(10, 10)
  }
  PanTopControl.prototype = new BMap.Control()
  PanTopControl.prototype.initialize = function (map) {
    var div = document.createElement('div')
    div.className = 'bmap-pan-control pan-top iconfont icon-arrow-down'
    div.onclick = function (e) {
      map.panBy(0, 100)
      if (typeof calltop === 'function') {
        calltop()
      }
    }
    map.getContainer().appendChild(div)
    return div
  }
  var panTopCtrl = new PanTopControl()
  // 添加到地图当中
  _bmap.addControl(panTopCtrl)

  // 创建控件
  function PanBottomControl () {
    this.defaultAnchor = BMAP_ANCHOR_BOTTOM_LEFT
    this.defaultOffset = new BMap.Size(10, 10)
  }
  PanBottomControl.prototype = new BMap.Control()
  PanBottomControl.prototype.initialize = function (map) {
    var div = document.createElement('div')
    div.className = 'bmap-pan-control pan-bottom iconfont icon-arrow-down'
    div.onclick = function (e) {
      map.panBy(0, -100)
      if (typeof calldown === 'function') {
        calldown()
      }
    }
    map.getContainer().appendChild(div)
    return div
  }
  var panBottomCtrl = new PanBottomControl()
  // 添加到地图当中
  _bmap.addControl(panBottomCtrl)

  _bmap.disableDoubleClickZoom()
  _bmap.disableDragging()
  return _bmap
}
export function addMarker (info, map) {
  // 给地图添加标记
  let lng = info.lng
  let lat = info.lat
  let text = info.text
  let color = info.color

  let marker

  let point = new BMap.Point(lng, lat)
  marker = new BMap.Marker(point)
  marker.initialize = function (map) {
    var div = document.createElement('div')
    div.style.position = 'absolute'
    div.style.width = '45px'
    div.style.height = '45px'
    div.style.lineHeight = '45px'
    div.style.fontSize = '40px'
    div.style.textAlign = 'center'
    div.style.color = color
    div.className = 'iconfont icon-dingwei'

    div.innerHTML =
      '<b style="font-size:18px;position:absolute;top:0;left:0px;color:#fff;line-height:1.8;width:100%">' +
      text +
      '</b>'

    map.getPanes().markerPane.appendChild(div)

    return div
  }
  marker.draw = function () {
    let point = this.getPosition()
    var position = map.pointToOverlayPixel(point)
    this.domElement.style.left = position.x - 22.5 + 'px'
    this.domElement.style.top = position.y - 45 + 'px'
  }

  map.addOverlay(marker)
  return marker
}
export function addMarker2 (info, map) {
  // 给地图添加标记
  let lng = info.lng
  let lat = info.lat
  let start_point = info.start_point
  let color = info.color

  let marker
  let totalDeg
  let point = new BMap.Point(lng, lat)
  marker = new BMap.Marker(point)
  marker.initialize = function (map) {
    var div = document.createElement('div')
    div.style.position = 'absolute'
    div.style.width = '20px'
    div.style.height = '20px'
    div.style.lineHeight = '20px'
    div.style.fontSize = '20px'
    div.style.textAlign = 'center'
    div.style.color = color

    let targetPos = map.pointToOverlayPixel(point)
    let curPos = map.pointToOverlayPixel(start_point)
    let deg = calRotation(curPos, targetPos)

    start_point = point
    totalDeg = deg + 13
    div.style.transform = 'rotate(' + (totalDeg % 360) + 'deg)'
    div.style.transformOrigin = targetPos.x - 10 + 'px ' + targetPos.y - 7.1875 + 'px'
    div.className = 'iconfont icon-dingwei1'

    map.getPanes().markerPane.appendChild(div)

    return div
  }
  marker.draw = function () {
    let point = this.getPosition()

    let targetPos = map.pointToOverlayPixel(point)
    let curPos = map.pointToOverlayPixel(start_point)
    let deg = calRotation(curPos, targetPos)

    start_point = point
    totalDeg = deg + 13
    this.domElement.style.transform = 'rotate(' + (totalDeg % 360) + 'deg)'
    var position = map.pointToOverlayPixel(point)
    this.domElement.style.left = position.x - 10 + 'px'
    this.domElement.style.top = position.y - 7.1875 + 'px'
  }

  map.addOverlay(marker)
  return marker
}
export function calRotation (curPos, targetPos) {
  var px = curPos.x
  var py = curPos.y
  var mx = targetPos.x
  var my = targetPos.y
  var x = Math.abs(px - mx)
  var y = Math.abs(py - my)
  var z = Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2))
  var cos = y / z
  var radina = Math.acos(cos) // 用反三角函数求弧度
  var angle = Math.floor(180 / (Math.PI / radina)) // 将弧度转换成角度

  if (mx > px && my > py) {
    // 鼠标在第四象限
    angle = 180 - angle
  }

  if (mx === px && my > py) {
    // 鼠标在y轴负方向上
    angle = 180
  }

  if (mx > px && my === py) {
    // 鼠标在x轴正方向上
    angle = 90
  }

  if (mx < px && my > py) {
    // 鼠标在第三象限
    angle = 180 + angle
  }

  if (mx < px && my === py) {
    // 鼠标在x轴负方向
    angle = 270
  }

  if (mx < px && my < py) {
    // 鼠标在第二象限
    angle = 360 - angle
  }

  return angle
}
export function clearWatch () {
  if (typeof navigator_id === 'undefined') {
    return
  }
  navigator.geolocation.clearWatch(navigator_id)
}

export const getAddressByPoint = location =>
  requestRaw(process.env.SITE_URL + '/bmap/geocoder/v2/', 'GET', {
    location: location,
    output: 'json',
    ak: process.env.VUE_APP_BMAP_AK,
    latest_admin: 1
  })
export const getPointByAddress = address =>
  requestRaw(process.env.SITE_URL + '/bmap/geocoder/v2/', 'GET', {
    address: address,
    output: 'json',
    ak: process.env.VUE_APP_BMAP_AK,
    latest_admin: 1
  })
export const getAddressByKeyword = (query, region) =>
  requestRaw(process.env.SITE_URL + '/bmap/place/v2/suggestion', 'GET', {
    query: query,
    region: region,
    city_limit: true,
    output: 'json',
    ak: process.env.VUE_APP_BMAP_AK
  })
export const getPointByIp = () =>
  requestRaw(process.env.SITE_URL + '/bmap/location/ip', 'GET', {
    coor: 'bd09ll',
    ak: process.env.VUE_APP_BMAP_AK
  })
export const getPointNearby = location =>
  requestRaw(process.env.SITE_URL + '/bmap/place/v2/search', 'GET', {
    query: '宾馆$酒店$住宅$餐饮$生活娱乐$公司$商务$学校$大厦$公寓$写字楼',
    output: 'json',
    location: location,
    ak: process.env.VUE_APP_BMAP_AK
  })
