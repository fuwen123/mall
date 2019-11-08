const regList = {
  number: new RegExp('^[0-9]*$'),
  email: /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((.[a-zA-Z0-9_-]{2,3}){1,2})$/,
  mobile: /^1[3-9]\d{9}$/
}

/**
 * 验证数据
 * @param {*} model  要验证的类型
 * @param {*} value  要验证的值
 */
export default function(model, value) {
  if (regList[model]) {
    return regList[model].test(value)
  } else {
    throw Error(`${model} - 模式不存在`)
  }
}
