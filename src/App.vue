<template>
  <div id="maps-app">
    <PublicFavoriteShareSideBar />
    <div class="content-wrapper">
      <MapContainer />
    </div>
  </div>
</template>

<script>
import MapContainer from "./components/MapContainer";
import PublicFavoriteShareSideBar from "./components/PublicFavoriteShareSideBar";

export default {
  name: "App",

  components: {
    MapContainer,
    PublicFavoriteShareSideBar
  }
};
</script>

<style lang="scss">
#maps-app {
  width: 100%;

  .content-wrapper {
    margin-left: 300px;
    height: 100%;
  }
}
</style>
