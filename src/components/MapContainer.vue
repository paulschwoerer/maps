<template>
  <div id="map-container"></div>
</template>

<script>
import L from "leaflet";

export default {
  name: "MapContainer",

  mounted() {
    this.initMap();
  },

  methods: {
    initMap() {
      const starImageUrl = OC.generateUrl(
        "/svg/core/actions/star-dark?color=000000"
      );
      const markerRedImageUrl = OC.generateUrl(
        "/svg/core/actions/address?color=EE3333"
      );
      const markerBlueImageUrl = OC.generateUrl(
        "/svg/core/actions/address?color=3333EE"
      );
      const markerGreenImageUrl = OC.generateUrl(
        "/svg/core/actions/address?color=33EE33"
      );
      const photoImageUrl = OC.generateUrl(
        "/svg/core/places/picture?color=000000"
      );
      const contactImageUrl = OC.generateUrl(
        "/svg/core/actions/user?color=000000"
      );
      const shareImageUrl = OC.generateUrl(
        "/svg/core/actions/share?color=000000"
      );

      this.map = L.map("map-container", {
        zoom: 2,
        zoomControl: false,
        maxZoom: 19,
        minZoom: 2,
        center: new L.LatLng(0, 0),
        maxBounds: new L.LatLngBounds(
          new L.LatLng(-90, 720),
          new L.LatLng(90, -720)
        ),
        layers: [],
        // right click menu
        contextmenu: false,
        contextmenuWidth: 160,
        contextmenuItems: [
          {
            text: t("maps", "Add a favorite"),
            icon: starImageUrl,
            callback: this.$emit("addFavorite")
          },
          {
            text: t("maps", "Place photos"),
            icon: photoImageUrl,
            callback: this.$emit("placePhotos")
            //}, {
            //    text: t('maps', 'Place photo folder'),
            //    icon: photoImageUrl,
            //    callback: photosController.contextPlacePhotoFolder
          },
          {
            text: t("maps", "Place contact"),
            icon: contactImageUrl,
            callback: this.$emit("placeContact")
          },
          {
            text: t("maps", "Share this location"),
            icon: shareImageUrl,
            callback: this.$emit("shareLocation")
          },
          "-",
          {
            text: t("maps", "Route from here"),
            icon: markerGreenImageUrl,
            callback: this.$emit("routeFrom")
          },
          {
            text: t("maps", "Add route point"),
            icon: markerBlueImageUrl,
            callback: this.$emit("routePoint")
          },
          {
            text: t("maps", "Route to here"),
            icon: markerRedImageUrl,
            callback: this.$emit("routeTo")
          }
        ]
      });

      const osm = L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
          attribution:
            '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
          noWrap: false,
          detectRetina: false,
          maxZoom: 19
        }
      );

      const roadsOverlay = L.tileLayer(
        "https://{s}.tile.openstreetmap.se/hydda/roads_and_labels/{z}/{x}/{y}.png",
        {
          maxZoom: 18,
          opacity: 0.7,
          attribution:
            '<a href="http://openstreetmap.se/" target="_blank">OpenStreetMap Sweden</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }
      );

      // tile layer selector
      const baseLayers = {
        OpenStreetMap: osm
        // "ESRI Aerial": ESRIAerial,
        // "ESRI Topo": ESRITopo,
        // OpenTopoMap: openTopo,
        // Dark: dark,
        // Watercolor: watercolor
      };
      const baseOverlays = {
        "Roads and labels": roadsOverlay
      };
      const controlLayers = L.control
        .layers(baseLayers, baseOverlays, {
          position: "bottomright",
          collapsed: false
        })
        .addTo(this.map);

      const locale = OC.getLocale();
      const imperial =
        locale === "en_US" ||
        locale === "en_GB" ||
        locale === "en_AU" ||
        locale === "en_IE" ||
        locale === "en_NZ" ||
        locale === "en_CA";
      const metric = !imperial;

      L.control
        .scale({ metric: metric, imperial: imperial, position: "bottomleft" })
        .addTo(this.map);

      L.control.zoom({ position: "bottomright" }).addTo(this.map);
    }
  }
};
</script>

<style lang="scss">
#map-container {
  position: relative;
  height: 100%;
  width: 100%;

  * {
    box-sizing: content-box;
  }

  .leaflet-tooltip {
    white-space: normal !important;
  }

  .leaflet-container {
    background: var(--color-main-background);
  }

  .leaflet-control-layers-base {
    line-height: 30px;
  }

  .leaflet-control-layers-selector {
    min-height: 0;
  }

  .leaflet-control-layers-toggle {
    background-size: 75% !important;
  }

  .leaflet-control-layers:not(.leaflet-control-layers-expanded) {
    width: 33px;
    height: 37px;
  }

  .leaflet-control-layers:not(.leaflet-control-layers-expanded) > a {
    width: 100%;
    height: 100%;
  }

  /* Adjust button styles to Nextcloud */
  .leaflet-touch {
    .leaflet-control-layers,
    .leaflet-bar {
      border: none;
      border-radius: var(--border-radius);
    }
  }

  /* Fix attribution overlapping map on mobile */
  .leaflet-control-attribution.leaflet-control {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 50vw;
  }
}
</style>
