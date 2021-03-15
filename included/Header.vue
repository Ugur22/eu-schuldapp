<template>
    <nb-header :style="styles.background">
      <nb-left  :style="{ flex:1}">
        <nb-button :style="{ width:50}" transparent :on-press="goBack" >
          <nb-icon :style="styles.icon" :name="Platform.OS === 'android' ? 'arrow-back' : 'arrow-round-back'" />
        </nb-button>
      </nb-left>
      <nb-body  :style="{ flex:0,justifyContent: 'center'}">
        <nb-title :style="styles.textHeader">{{pageTitle}}</nb-title>
      </nb-body>
      <nb-right :style="{ flex:1}">
        <nb-button :style="{ width:50}" transparent >
          <!-- <nb-icon :style="styles.icon" name="information-circle"/>  -->
        </nb-button>
      </nb-right>
    </nb-header>
</template>
<script>
import {styles} from '../pages/styling/style';
import { Platform } from "react-native";

  export default {
	props: {
	  activeBtn: {
		type: String
      },
      method: {type: Function},
      pageTitle:''
	},
	data() {
	  return {
      styles,
      Platform
	  };
	},
	methods: {
    goBack: function(){
        this.method();
      },
	  }
  }
</script>