<template>
	<div class="container">
		<div class="common-header-wrap">
			<mt-header :title="getTitle" class="common-header">
				<mt-button slot="left" icon="back" @click="goBack"></mt-button>
			</mt-header>
		</div>

		<mt-field label="组名称" v-model="seller_group_name" ></mt-field>
		<div @click="menuVisible=true">
			<mt-cell title="操作权限" is-link />
		</div>
		<div @click="smtVisible=true">
			<mt-cell title="消息接收权限" is-link />
		</div>
		<mt-button class="ds-button-large" type="primary" v-on:click="submit">保存</mt-button>
		<mt-popup v-model="menuVisible" position="right" class="common-popup-wrapper">
			<div class="common-header-wrap">
				<mt-header title="操作权限" class="common-header">
					<mt-button slot="left" icon="back" @click="menuVisible=false"></mt-button>
				</mt-header>
			</div>
			<div class="common-popup-content">
				<div v-for="(item,index) in menu_options">
					<mt-checklist
							:title="item.text"
							v-model="menu"
							:options="item.options">
					</mt-checklist>
				</div>
			</div>
		</mt-popup>
		<mt-popup v-model="smtVisible" position="right" class="common-popup-wrapper">
			<div class="common-header-wrap">
				<mt-header title="消息接收权限" class="common-header">
					<mt-button slot="left" icon="back" @click="smtVisible=false"></mt-button>
				</mt-header>
			</div>
			<div class="common-popup-content">
				<mt-checklist
						v-model="smt"
						:options="smt_options">
				</mt-checklist>
			</div>
		</mt-popup>
	</div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import { getCommonData, getAccountGroupInfo, editAccountGroup } from '../../../api/sellerAccountGroup'
export default {
  components: {
  },
  computed: {
    isAddMode () {
      let mode = this.$route.query.action
      // add: 添加地址，edit: 编辑地址
      if (mode === 'add') {
        return true
      } else {
        return false
      }
    },
    getTitle () {
      if (this.isAddMode) {
        return '新增帐号组'
      } else {
        return '修改帐号组'
      }
    }

  },
  data () {
    return {
      seller_group_name: '',
      menuVisible: false,
      smtVisible: false,
      group_id: 0,
      smt_options: [],
      menu_options: [],
      smt: [],
      menu: []
    }
  },
  created: function () {
    getCommonData().then(res => {
      let temp = res.result.smt_list
      for (var i in temp) {
        let item = temp[i]
        this.smt_options.push({
          label: item.storemt_name,
          value: item.storemt_code
        })
      }
      let menu_list = res.result.seller_menu
      for (var i in menu_list) {
        let item = menu_list[i]
        // this.menu[i] = []
        let temp = []
        for (var j in item.submenu) {
          let menu = item.submenu[j]
          temp.push({
            label: menu.text,
            value: menu.controller
          })
        }
        this.menu_options.push({
          key: i,
          text: item.text,
          options: temp
        })
      }
    }).catch(function (error) {
      Toast(error.message)
    })
    if (!this.isAddMode) {
      this.group_id = this.$route.query.group_id
      getAccountGroupInfo(this.group_id).then(res => {
        this.seller_group_name = res.result.group_name
        this.menu = res.result.group_limits
        this.smt = res.result.smt_limits
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
    submit () {
      if (!this.seller_group_name) {
        Toast('请填写组名称')
        return
      }
      Indicator.open()
      editAccountGroup(this.group_id, this.seller_group_name, this.menu, this.smt).then(
        (response) => {
          this.$router.go(-1)
        }, (error) => {
          Indicator.close()
          Toast(error.message)
        })
    }

  }
}
</script>

<style lang="scss" scoped>
	.right-arrow{transform: rotate(-90deg);color:#ddd;font-size: .6rem;display: inline-block;}
	.input-wrap{position: relative;
		i{position: absolute;right:0;top:0;line-height: 2.4rem;display: block;width:2rem;text-align: center;font-size: 1rem}
	}
</style>
