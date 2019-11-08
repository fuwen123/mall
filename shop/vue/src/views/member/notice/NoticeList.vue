<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="通知消息" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
      <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
    <div
      class="body"
      v-if="notice_list && notice_list.length"

    >
      <div
        class="notice-message-body"
        v-for="(item, index) in notice_list"
        :key="item.message_id"
      >
          <p>{{$moment.unix(item.message_time).format('YYYY-MM-DD HH:mm')}}</p>
          <div class="notice-track">
              <div class="notice-status">
                  <p class="title">{{ message_type_text[item.message_type] }}</p>
                  <p class="content">{{ item.message_body }}</p>
              </div>

          </div>

      </div>
    </div>
    <empty-record v-else-if="notice_list && !notice_list.length"></empty-record>
  </div>
  </div>
</template>

<script>
import { getNoticeList } from '../../../api/memberNotice'
import { Toast, Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
export default {
  name: 'NoticeList',
  components: {
    EmptyRecord
  },
  data () {
    return {
        params: { 'page': 0, 'per_page': 10 },
        loading: false, // 是否加载更多
        isMore: true, // 是否有更多
      message_type_text: ['私信', '系统消息', '留言'],
      wrapperHeight: 0,
      notice_list: false,
    }
  },
  created () {

  },

  mounted () {
    this.wrapperHeight = document.documentElement.clientHeight - 90
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
      loadMore () {
          this.loading = true
          this.params.page = ++this.params.page
          if (this.isMore) {
              this.loading = false
              this.getNoticeList(true)
          }
      },
    getNoticeList () {
      Indicator.open()

          getNoticeList(this.params).then(res => {
            Indicator.close()
              if (res.result.hasmore) {
                  this.isMore = true
              } else {
                  this.isMore = false
              }

            let temp = res.result.notice_list
            if (temp) {
              if (!this.notice_list) {
                this.notice_list = temp
              } else {
                this.notice_list = this.notice_list.concat(temp)
              }
            }
          }).catch(function (error) {
            Indicator.close()
            Toast(error.message)
          })


    }
  }
}
</script>

<style scoped lang="scss">
    .container {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        .header {

            border-bottom: 1px solid #e8eaed;
            position: fixed;
            width: 100%;
            z-index: 1;
        }
        .body {
            width: 100%;
            .notice-message-body {
                width: 100%;
                height: 100%;
                > p {
                    text-align: center;
                    margin-top:1rem;
                    margin-bottom:0.5rem;
                    font-size:0.6rem;
                    color: #7c7f88;
                }
                .notice-track {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    height: 100%;
                    background: rgba(255, 255, 255, 1);
                    border-radius:0.1rem;
                    margin: 0 0.5rem;
                    .notice-status {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        flex-wrap: wrap;
                        margin: 0.6rem 0.75rem 0.7rem 0.75rem;
                        .title {
                            font-size:0.7rem;
                            color: #333;
                            margin: 0 0 0.7rem 0;
                        }
                        .content {
                            font-size: 0.65rem;
                            color: #666;
                            width: 100%;
                            height: 100%;
                        }
                    }
                    .arrow-right {
                        width:0.25rem;
                        height: 0.5rem;
                        margin-right: 0.6rem;
                        margin-left: 0.65rem;
                    }
                }
            }
        }
    }
</style>
