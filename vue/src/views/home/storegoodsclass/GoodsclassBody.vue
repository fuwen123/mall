<template>
  <div class="ui-category-body">
    <div class="category-flex">
      <div class="category-sidebar">
        <ul>
          <li class="item"
              v-for='item in items'
              v-bind:key="item.id"
              v-on:click='onItemClick(item)'
              v-bind:class="{'sidbaractive': (currentItem && item.id == currentItem.id), 'noActive' : (currentItem && item.id != currentItem.id)}">
            <a>{{ item.value }}</a>
          </li>
        </ul>
      </div>
      <div class="category-content" v-if='currentItem && currentItem.children'>
        <ul>
          <li class="item" v-for='item in currentItem.children' v-bind:key = "item.id" v-on:click='goProduct(item.id)'>
            <a>{{item.value}}</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { Indicator, Toast } from 'mint-ui'
import { getStoreGoodsClass } from '../../../api/homestoredetail'
export default {
  data () {
    return {
      items: false,
      currentItem: false
    }
  },
  computed: {

  },
  created () {
    this.getGoodsclassList()
  },
  methods: {
    onItemClick (item) {
      this.currentItem = item
    },
    getGoodsclassList () {
      if (!(this.items && this.items.length)) {
        Indicator.open()
      }
      getStoreGoodsClass(this.$route.query.id).then((res) => {
        this.items = res.result.store_goods_class
        if (this.items.length > 0) {
          this.currentItem = this.items[0]
        }
        Indicator.close()
      }, (error) => {
        Toast(error.message)
        Indicator.close()
      })
    },

    goProduct (id) {
      let params = { id:this.$route.query.id,'gc_id': id }
      this.$router.push({ 'name': 'HomeStoreGoodslist', 'query': params })
    }
  }
}
</script>

<style scoped lang="scss">
  .ui-category-body{
    width: 100%;
    .category-flex {
      display: flex;
      display: -webkit-box;
      display: -moz-box;
      display: -ms-flexbox;
      display: -webkit-flex;
      width: 100%;
      position: absolute;
      bottom: 0;
      width: 100%;
      top:2.5rem;
      .category-sidebar {
        flex-basis:7rem;
        background-color: #F0F2F5;
        overflow-y: scroll;
        ul {
          border-right: 0.5px solid #E8EAED;
          li {
            display: block;
            padding:0.8rem 0.25rem;
            a {
              color: #333;
              display: -webkit-box;
              -webkit-box-orient: vertical;
              -webkit-line-clamp: 1;
              overflow: hidden;
              font-size:0.7rem;
              text-align: center;
            }
          }
          li.noActive {
            background-color: #F0F2F5;
            border-left:0.1rem solid transparent;
            a {
              color: #333;
            }
          }
          li.sidbaractive{
            background-color: #FFFFFF;
            border-left:0.1rem solid #e93b3d;
            a {
              font-weight:600;
              color: $primaryColor;
            }
          }
        }
      }
      .category-content {
        width: 100%;
        background-color: #fff;
        overflow: auto;
        ul {
          height: 100%;
          li {
            display: block;
            padding:1rem 0;
            text-align: center;
            cursor: pointer;
            border-bottom: 0.5px solid rgb(232,234,237);
            a {
              color: #333;
              font-size:0.7rem;
              text-align: center;
            }
          }
        }
      }
    }
  }
</style>
