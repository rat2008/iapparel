(function ($, _, Backbone) {
	
	var AreaModel = Backbone.Model.extend({
		defaults: function () {
			var colors = "#F4B300 #78BA00 #2673EC #AE113D #632F00 #B01E00 #7200AC #4617B4 #006AC1 #008287 #199900 #00C13F #FF981D #FF2E12 #FF1D77 #AA40FF #1FAEFF #56C5FF #00D8CC #91D100 #E1B700 #FF76BC #00A4A4 #FF7D23".split(" "),
			startX = _.random($(window).width() - 200),
			startY = _.random(100);

			return {
				"id": _.uniqueId(),
				"startX": startX,
				"startY": startY,
				"endX": startX + 100 + _.random(100),
				"endY": startY + 100 + _.random(100),
				"zIndex": _.uniqueId(),
				"color": colors[_.random(colors.length - 1)],
				"isChanging": false
			}
		},
		setVertex: function (direction, top, left) {
			var values = {};

			if (direction[0] === 'n') {
				values.startY = top;
			} else if (direction[0] === 's') {
				values.endY = top;
			}

			if (direction[1] === 'w') {
				values.startX = left;
			} else if (direction[1] === 'e') {
				values.endX = left;
			}

			this.set(values);
		}
	}),
	AreaCollection = Backbone.Collection.extend({
		model: AreaModel
	}),
	AreaView = Backbone.View.extend({
		areaTemplate: _.template($("#template-demo-editor-area").html()),
		r_areaID: /demo-editor-area-(.*)/,
		initialize: function () {
			this.collection.on("add", this.addArea, this);
			this.collection.on("change", this.changeArea, this);
			this.collection.on("remove", this.removeArea, this);
		},
		addArea: function (model) {
			this.$el.append(this.areaTemplate(model.toJSON()));
		},
		changeArea: function (model) {
			// This is a very inefficient way of applying changes and 
			// causes much junk, but it's alright for this demo
			$("#demo-editor-area-" + model.get("id")).replaceWith(this.areaTemplate(model.toJSON()));
		},
		removeArea: function (model) {
			$("#demo-editor-area-" + model.get("id")).remove();
		},
		startResize: function (e) {
			var handle = $(e.target),
			areaNode = handle.parent(),
			areaId = (areaNode[0].id || "demo-editor-area--1").match(this.r_areaID)[1],
			areaModel = this.collection.findWhere({"id": areaId});

			if (areaModel && !areaModel.get("isChanging")) {
				areaModel.set("isChanging", true);

				this.currentlyResized = areaModel;
				this.currentlyResizedDirection = handle.data("direction");
			}
		},
		startMove: function (e) {
			var areaNode = $(e.currentTarget).parent(),
			areaId = (areaNode[0].id || "demo-editor-area--1").match(this.r_areaID)[1],
			areaModel = this.collection.findWhere({"id": areaId});

			if (areaModel && !areaModel.get("isChanging")) {
				areaModel.set({
					"isChanging": true,
					"zIndex": _.uniqueId() // we make use of the automatic increment of _.uniqueId
				});

				this.currentlyMoved = areaModel;
				this.currentlyMovedBefore = {x: e.pageX, y: e.pageY};
			}
		},
		drag: function (e) {
			if (this.currentlyResized) {
				// Resize
				var dir = this.currentlyResizedDirection,
				model = this.currentlyResized,
				offsetY = 45; // This is because of the undobutton-bar
				model.setVertex(dir, e.pageY - offsetY, e.pageX);
			} else if (this.currentlyMoved) {
				// Move
				var before = this.currentlyMovedBefore,
				model = this.currentlyMoved,
				deltaX = e.pageX - before.x,
				deltaY = e.pageY - before.y;

				this.currentlyMovedBefore = {x: e.pageX, y: e.pageY};

				model.set({
					"startX": model.get("startX") + deltaX,
					"startY": model.get("startY") + deltaY,
					"endX": model.get("endX") + deltaX,
					"endY": model.get("endY") + deltaY,
				})
			}
		},
		endDrag: function () {
			if (this.currentlyResized) {
				this.currentlyResized.set("isChanging", false);
				delete this.currentlyResized;
				delete this.currentlyResizedDirection;
			} else if (this.currentlyMoved) {
				this.currentlyMoved.set("isChanging", false);
				delete this.currentlyMoved;
				delete this.currentlyMovedBefore;
			}
		},
		events: {
			"mousedown .demo-editor-area-content": "startMove",
			"mousedown .demo-editor-handle": "startResize",
			"mousemove": "drag",
			"mouseup": "endDrag"
		}
	});

	var areaCollection = window.demoEditor = new AreaCollection,
	areaView = new AreaView({
		collection: areaCollection,
		el: $(".demo-editor-canvas")
	});

	areaCollection.add([{}, {}, {}]);

	$(".demo-editor-new-area").click(function () {
		areaCollection.add([{}]);
	})
})(window.jQuery, window._, window.Backbone);