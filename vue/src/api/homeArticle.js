import { requestApi } from '../util/network'

// 系统文章
export const getDocumentInfo = type =>
  requestApi('/Document/agreement', 'GET', {
    type: type
  })

// 获取文章分类
export const getArticleclassList = () => requestApi('/Articleclass/index', 'GET', {})

// 获取文章列表
export const getArticleList = (params, acId) =>
  requestApi('/Article/article_list', 'GET', {
    ac_id: acId,
    page: params.page // 当前第几页
  })

// 获取文章详情
export const getArticleDetail = articleID =>
  requestApi('/Article/article_show', 'GET', {
    article_id: articleID
  })
