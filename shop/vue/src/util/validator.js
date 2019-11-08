export default {
  /**
   * @param {*要判断的参数值} value
   * 是否是数值
   */
  isNumber (value) {
    let reg = new RegExp('^[0-9]*$')
    return reg.test(value)
  },
  /**
   *
   * @param {*要判断的参数值} value
   * 是否是邮箱
   */
  isEmail (value) {
    var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/
    return reg.test(value)
  }
}
