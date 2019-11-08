export default {
  fetch(key) {
    return JSON.parse(window.localStorage.getItem(key) || '[]')
  },
  save(key, value) {
    window.localStorage.setItem(key, JSON.stringify(value))
  },
  stopPrevent(event) {
    let e = event || window.event
    if (e.preventDefault) {
      e.preventDefault()
    } else {
      window.event.returnValue = false // IE
    }
  },
  // arrayFilter: 数组去重
  arrayFilter(array) {
    let newAray = []
    for (let i = 0, len = array.length - 1; i <= len; i++) {
      if (newAray.indexOf(array[i]) < 0) {
        newAray.push(array[i])
      }
    }
    return newAray
  },
  fillTheScreen(obj) {
    const isWX = /micromessenger/.test(navigator.userAgent.toLowerCase())
    // why? document.documentElement.clientHeight - document.documentElement.offsetHeight
    let height = isWX ? document.documentElement.clientHeight : document.documentElement.offsetHeight
    if (!obj.target || !obj.totalHeight) return
    height = 1 - obj.totalHeight / height
    obj.target.style.height = height * 100 + 'vh'
  },
  /**
   *
   * @param start  开始展示的字符
   * @param end 结束字符展示位置
   * @param target 目标字符
   * @param length
   * @returns {string|string}
   */
  replaceStr(target, start, end, length) {
    let str = ''
    if (start) {
      str = target.substr(start, length) + '***'
    } else if (end) {
      str = '***' + target.substr(end, length)
    } else {
      str = target.substr(0, 1) + '***' + target.substr(target.length - 1, 1)
    }
    return str
  },
  /**
   *
   * @param date 日期
   * @param format 显示的格式
   * @returns {string}
   */
  formatDate(date, format) {
    if (date) {
      return this.$moment(date).format(format)
    }
  },
  padLeftZero(str) {
    return ('00' + str).substr(str.length)
  },
  /**
   * 格式化价格（保留两位小数）
   * @param price 价格
   * @returns {string}
   */
  currencyPrice(price) {
    return parseFloat(price).toFixed(2)
  },
  /**
   * 是否是数值
   * @param value 要判断的参数值
   * @returns {boolean}
   */
  isNumber(value) {
    let reg = new RegExp('^[0-9]*$')
    if (!reg.test(value)) {
      return false
    }
    return true
  },
  /**
   *
   * @param beginAt 开始时间（时间戳）
   * @param endAt 结束时间（时间戳）
   * @returns {number}
   */
  activityStatus(beginAt, endAt) {
    let status = -1 // (0: 未开始；1: 进行中；2: 已过期)
    let timestamp = Date.parse(new Date()) / 1000
    if (beginAt > timestamp) {
      status = 0
    } else if (timestamp > beginAt && timestamp < endAt) {
      status = 1
    } else if (timestamp > endAt) {
      status = 2
    }
    return status
  },
  /**
   * 把秒数换为*天*时*分*秒的时间格式
   * @param interval 时间间隔（单位为s）
   * @returns {string|*}
   */
  formatTimeInterval(interval) {
    let format = null
    let day = parseInt(interval / 60.0 / 60.0 / 24.0)
    let hour = parseInt((interval / 60 / 60) % 24)
    let minute = parseInt((interval / 60) % 60)
    let second = interval % 60
    format = day + ' 天 ' + hour + ' 时 ' + minute + ' 分 ' + second + ' 秒'
    return format
  },

  /**
   * 获取未读消息数
   * @param zhiManager
   * @param scoped
   * @param key
   */
  getunreadCount(zhiManager, scoped, key) {
    zhiManager.on('unread.count', function(data) {})
    zhiManager.on('receivemessage', function(ret) {
      scoped.key = ret
    })
  },
  /**
   *
   * @param timestamp 时间戳转化为日期
   * @returns {string}
   */
  timestampToTime(timestamp) {
    var date = new Date(timestamp * 1000) // 时间戳为10位需*1000，时间戳为13位的话不需乘1000
    var Y = date.getFullYear() + '-'
    var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-'
    var D = date.getDate() + ' '
    var h = date.getHours() + ':'
    var m = date.getMinutes() + ':'
    var s = date.getSeconds()
    return Y + M + D + h + m + s
  },

  // 设置cookie
  setCookie: function(cname, cvalue, exdays) {
    var d = new Date()
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000)
    var expires = 'expires=' + d.toUTCString()
    document.cookie = cname + '=' + cvalue + '; ' + expires + '; path=/;'
  },
  // 获取cookie
  getCookie: function(cname) {
    var name = cname + '='
    var ca = document.cookie.split(';')
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i]
      while (c.charAt(0) === ' ') c = c.substring(1)
      if (c.indexOf(name) !== -1) return c.substring(name.length, c.length)
    }
    return ''
  },
  // 清除cookie
  clearCookie: function(cname) {
    this.setCookie(cname, '', -1)
  },
  getUrlKey: function(url, name) {
    return (
      decodeURIComponent(
        (new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(url) || [,''])[1].replace(/\+/g, '%20')
      ) || null
    )
  }
}
