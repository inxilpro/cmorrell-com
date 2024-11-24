<x-layout title="Joey T calculator - Chris Morrell">
	
	<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
		Joey T calculator
	</h1>
	
	<p class="text-xl lg:text-2xl leading-normal my-4">
		How many <a class="font-slant text-blue-900 hover:underline hover:text-blue-600" href="https://bsky.app/profile/cmorrell.com/post/3lbpi27fkts2c" target="_blank">Joey T’s</a>
		are you? This easy calculator will help you find out!
	</p>
	
	<div x-data="joeyt" class="my-4 border rounded p-4">
		<h2 class="text-xl font-bold font-slant text-gray-800">
			Your Height:
		</h2>
		
		<div class="lg:flex -mx-2 mt-4">
			<div class="mb-2 flex-1 flex flex-col justify-center mx-2 border rounded">
				<label for="feet" class="font-bold mb-1 bg-gray-100 text-center p-2">
					Feet
				</label>
				<div class="flex justify-center items-baseline p-2 -mx-1">
					<input
						id="feet"
						class="border border-gray-300 p-2 rounded focus:border-gray-500 mx-1"
						type="number"
						min="0"
						step="1"
						x-model.number="feet"
					/>
					<span class="mx-1 font-bold">
						’
					</span>
				</div>
			</div>
			<div class="mb-2 flex-1 flex flex-col justify-center mx-2 border rounded">
				<label for="inches" class="font-bold mb-1 bg-gray-100 text-center p-2">
					Inches
				</label>
				<div class="flex justify-center items-baseline p-2 -mx-1">
					<input
						id="inches"
						class="border border-gray-300 p-2 rounded focus:border-gray-500 mx-1"
						type="number"
						min="0"
						step=".25"
						x-model.number="inches"
					/>
					<span class="mx-1 font-bold">
						”
					</span>
				</div>
			</div>
		</div>
		
		<div class="py-12">
			<h3 class="text-4xl font-bold font-slant text-gray-800 text-center">
				You are <span class="text-6xl text-purple-700" x-text="height"></span> Joey T<span x-text="1 === height ? '' : '’s'"></span> tall
			</h3>
		</div>
		
	</div>
	
	<script>
	document.addEventListener('alpine:init', () => {
		Alpine.data('joeyt', () => ({
			feet: 5,
			inches: 11.75,
			get height() {
				return ((this.feet * 12) + this.inches) / (6 * 12);
			}
		}));
	});
	</script>

</x-layout>
