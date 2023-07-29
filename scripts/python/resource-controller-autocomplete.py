import re
namespace = "namespace Larammerce\\Http\\Controllers\\Admin"
path = "app/Http/Controllers/Admin"


variable_name = lambda s: '$' + s[:1].lower() + s[1:] if s else ''

def write_header_to_controller(cf, mn):
	cf.write("<?php\n\n")
	cf.write(namespace+";\n\n")
	cf.write("use Larammerce\\Models\\{};\n".format(mn))
	cf.write("use Illuminate\\Http\\Request;\n\n")
	cf.write("use Larammerce\\Http\\Requests;\n")
	cf.write("use Larammerce\\Http\\Controllers\\Controller;\n\n")

def generate_class_definition(cf, mn):
	cf.write("class {}Controller extends Controller\n".format(mn))
	cf.write("{\n");

def snake_case(name):
    s1 = re.sub('(.)([A-Z][a-z]+)', r'\1_\2', name)
    return re.sub('([a-z0-9])([A-Z])', r'\1_\2', s1).lower()

def dash_case(name):
	return snake_case(name).replace('_', '-')

def write_with_indent(cf, txt, indent):
	for i in xrange(indent):
		cf.write('\t')
	cf.write(txt)
	cf.write('\n')

def generate_method(cf, name, lines, php_doc, args=[]):
	cf.write(php_doc)
	write_with_indent(cf, "public function {}({})".format(name, ", ".join(args)), 0)
	write_with_indent(cf, "{", 1)
	for line in lines:
	    write_with_indent(cf, line, 2)
	write_with_indent(cf, "}", 1)
	cf.write("\n")

def generate_index_method(cf, mn, mr):
	php_doc = '''\
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	'''
	lines = []
	lines.append("{}s = {}::with({})->get();".format(variable_name(mn),mn,','.join(mr)))
	lines.append("return view(\'admin.pages.{}.index\', compact(\'{}s\'));".format(dash_case(mn), variable_name(mn)[1:]))
	generate_method(cf, "index", lines, php_doc)


def generate_create_method(cf, mn):
	php_doc = '''\
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	'''
	lines = []
	lines.append("return view(\'admin.pages.{}.create\');".format(dash_case(mn)))
	generate_method(cf, "create", lines, php_doc)

def generate_store_method(cf, mn):
	php_doc = '''\
	/**
	 * Store a newly created resource in storage.
	 *
	 * @rules()
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	'''
	lines = []
	lines.append("{}::create($request->all());".format(mn))
	lines.append("return redirect()->route('admin.pages.{}.index');".format(dash_case(mn)))
	generate_method(cf, "store", lines, php_doc, ["Request $request"])

def generate_show_method(cf, mn):
	php_doc = '''\
	/**
	 * Display the specified resource.
	 *
	 * @param  {} ${}
	 * @return \Illuminate\Http\Response
	 */
	'''.format(mn, snake_case(mn))
	lines = []
	lines.append("//")
	generate_method(cf, "show", lines, php_doc, ["{} ${}".format(mn, snake_case(mn))])

def generate_edit_method(cf, mn, mr):
	php_doc = '''\
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  {} ${}
	 * @return \Illuminate\Http\Response
	 */
	'''.format(mn, snake_case(mn))
	lines = []
	lines.append("${}->load({});".format(snake_case(mn), ','.join(mr)))
	lines.append("return view('admin.pages.{}.edit')->with(['{}' => ${}]);".format(dash_case(mn), variable_name(mn)[1:], snake_case(mn)))
	generate_method(cf, "edit", lines, php_doc, ["{} ${}".format(mn, snake_case(mn))])

def generate_update_method(cf, mn):
	php_doc = '''\
	/**
	 * Update the specified resource in storage.
	 *
	 * @rules()
	 * @param  \Illuminate\Http\Request  $request
	 * @param  {} ${}
	 * @return \Illuminate\Http\Response
	 */
	'''.format(mn, snake_case(mn))
	lines = []
	lines.append("${}->update($request->all());".format(snake_case(mn)))
	lines.append("return redirect()->route('admin.pages.{}.index');".format(dash_case(mn)))
	generate_method(cf, "update", lines, php_doc, ["Request $request", "{} ${}".format(mn, snake_case(mn))])

def generate_destroy_method(cf, mn):
	php_doc = '''\
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  {} ${}
	 * @return \Illuminate\Http\Response
	 */
	'''.format(mn, snake_case(mn))
	lines = []
	lines.append("${}->delete();".format(snake_case(mn)))
	lines.append("return back();")
	generate_method(cf, "destroy", lines, php_doc, ["{} ${}".format(mn, snake_case(mn))])

def generate_attach_method(cf, mn, attachable_model):
	table, field = attachable_model.split('-')
	php_doc = '''\
	/**
	 * Attach array of {0} to the {1}.
	 *
     * @rules({0}="array", {0}.*="exists:{2}\,id")
     * @param Request $request
     * @param {3} ${1}
	 * @return \Illuminate\Http\Response
	 */
	'''.format(field, snake_case(mn), table, mn)
	lines = []
	lines.append("${}->{}()->detach();".format(snake_case(mn), field))
	lines.append("${0}->{1}()->attach($request->get('{1}'));".format(snake_case(mn), field))
	lines.append("return redirect()->route('admin.pages.{}.index');".format(dash_case(mn)))
	generate_method(cf, "attach{}".format(field.title()), lines, php_doc, ["Request $request", "{} ${}".format(mn, snake_case(mn))])	

def main():
	print "This script create ready resource controller in following path:"
	print path
	print "-"*50
	model_name = raw_input("Enter Model Name: ")
	model_relations = map(lambda r: '\''+r+'\'',raw_input("Enter Relations(seperated with space): ").split())
	attachable_models = raw_input("Enter Attachable Models(table-property E.X. system_roles-roles): ").split()
	controller_file = open("../{}/{}Controller.php".format(path,model_name), 'w')
	write_header_to_controller(controller_file, model_name)
	generate_class_definition(controller_file, model_name)
	generate_index_method(controller_file, model_name, model_relations)
	generate_create_method(controller_file, model_name)
	generate_store_method(controller_file, model_name)
	generate_show_method(controller_file, model_name)
	generate_edit_method(controller_file, model_name, model_relations)
	generate_update_method(controller_file, model_name)
	generate_destroy_method(controller_file, model_name)

	for attachable_model in attachable_models:
		generate_attach_method(controller_file, model_name, attachable_model)

	controller_file.write("}\n")

	print "{}Controller has been creates successfuly!".format(model_name)


if  __name__ == "__main__":
    main()