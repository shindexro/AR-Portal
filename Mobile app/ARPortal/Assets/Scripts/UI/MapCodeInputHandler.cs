using System.Collections.Generic;
using System.Text;
using System.Text.RegularExpressions;
using UnityEngine;
using UnityEngine.UI;

public class MapCodeInputHandler : MonoBehaviour
{
    public InputField mapCodeField;

    private static readonly HashSet<char> AcceptChars =
        new HashSet<char>("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789".ToCharArray());
    private static readonly Regex MapCodeFormat = new Regex("^[a-zA-Z0-9]{4} - [a-zA-Z0-9]{4}$");

    public void OnValueChanged()
    {
        string rawCode = mapCodeField.text;
        string formattedCode = FormatMapCode(rawCode);

        mapCodeField.text = formattedCode;
        
        if (mapCodeField.caretPosition == 4 && formattedCode.Length == 7)
        {
            mapCodeField.caretPosition = 7;
        }
        else if (5 <= mapCodeField.caretPosition && mapCodeField.caretPosition <= 7 && rawCode.Length == 6)
        {
            mapCodeField.text = mapCodeField.text.Substring(0, 3);
        }
    }

    // Change the map code into the format "XXXX - XXXX" with capitalised letter
    // e.g. ab => AB, abcd1234 => ABCD - 1234
    public static string FormatMapCode(string code)
    {
        int length = 0;
        StringBuilder formattedCode = new StringBuilder();

        foreach (char c in code)
        {
            if (length >= 8)
                break;
            if (!AcceptChars.Contains(c))
                continue;
            length++;
            formattedCode.Append(char.ToUpper(c));
            if (length == 4)
            {
                formattedCode.Append(" - ");
            }
        }

        return formattedCode.ToString();
    }

    // Input string with format "XXXX - XXXX" and output a string with removed dash and lower case letters
    // e.g. ABCD-1234 => abcd1234
    public static string DecodeMapCode(string mapCode)
    {
        return (mapCode.Substring(0, 4) + mapCode.Substring(7, 4)).ToLower();
    }

    // Check if the map code matches the format "XXXX - XXXX"
    public static bool MapCodeFormatValid(string mapCode)
    {
        return MapCodeFormat.IsMatch(mapCode);
    }
}