param ([string]$source = $(Throw "You have to specify a source path."))

$extensionSize = 3

if ($source.EndsWith("docx"))
{
	$extensionSize = 4
}

$word = new-object -ComObject "word.application"
$doc  = $word.documents.open($source)

$doc.SaveAs($source.Substring(0, $source.Length - $extensionSize) + "pdf", 17)
$doc.Close()

ps winword | kill